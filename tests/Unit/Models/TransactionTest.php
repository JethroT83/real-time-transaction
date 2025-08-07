<?php

use App\Models\Transaction;
use App\Enums\AccountType;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->transaction = Transaction::factory()->create([
        'timestamp' => now(),
        'amount' => 100.50,
        'description' => 'Test Transaction',
        'accountType' => AccountType::CHECKING,
    ]);
});

test('transaction has correct fillable attributes', function () {
    $fillable = [
        'timestamp',
        'amount',
        'description',
        'accountType',
    ];

    expect($this->transaction->getFillable())->toBe($fillable);
});

test('transaction casts attributes correctly', function () {
    $casts = [
        'timestamp' => 'datetime',
        'amount' => 'decimal:2',
        'accountType' => AccountType::class,
    ];

    expect($this->transaction->getCasts())->toMatchArray($casts);
});

test('can create transaction with factory', function () {
    $transaction = Transaction::factory()->create();

    expect($transaction)
        ->toBeInstanceOf(Transaction::class)
        ->and($transaction->exists)->toBeTrue()
        ->and($transaction->id)->toBeInt()
        ->and($transaction->timestamp)->toBeInstanceOf(DateTime::class)
        ->and($transaction->amount)->toBeNumeric()
        ->and($transaction->description)->toBeString()
        ->and($transaction->accountType)->toBeInstanceOf(AccountType::class);
});

test('can create deposit transaction', function () {
    $transaction = Transaction::factory()
        ->deposit()
        ->create();

    expect($transaction->amount)
        ->toBeGreaterThan(0)
        ->toBeGreaterThanOrEqual(100)
        ->toBeLessThanOrEqual(5000)
        ->and($transaction->description)->toStartWith('Deposit -');
});

test('can create withdrawal transaction', function () {
    $transaction = Transaction::factory()
        ->withdrawal()
        ->create();

    expect($transaction->amount)
        ->toBeLessThan(0)
        ->toBeLessThanOrEqual(-100)
        ->toBeGreaterThanOrEqual(-5000)
        ->and($transaction->description)->toStartWith('Withdrawal -');
});

test('can filter transactions by account type', function () {
    // Clear existing transactions
    Transaction::query()->delete();

    // Create transactions for each account type
    Transaction::factory()->count(3)->forAccountType(AccountType::CHECKING->value)->create();
    Transaction::factory()->count(2)->forAccountType(AccountType::SAVINGS->value)->create();
    Transaction::factory()->count(4)->forAccountType(AccountType::CREDIT->value)->create();

    $checkingTransactions = Transaction::query()
        ->where('accountType', AccountType::CHECKING)
        ->get();

    $savingsTransactions = Transaction::query()
        ->where('accountType', AccountType::SAVINGS)
        ->get();

    $creditTransactions = Transaction::query()
        ->where('accountType', AccountType::CREDIT)
        ->get();

    expect($checkingTransactions)->toHaveCount(3)
        ->and($savingsTransactions)->toHaveCount(2)
        ->and($creditTransactions)->toHaveCount(4);
});

test('can sort transactions by timestamp', function () {
    // Clear existing transactions
    Transaction::query()->delete();

    // Create transactions with different timestamps
    $oldestTransaction = Transaction::factory()->create([
        'timestamp' => now()->subDays(2),
    ]);

    $middleTransaction = Transaction::factory()->create([
        'timestamp' => now()->subDay(),
    ]);

    $newestTransaction = Transaction::factory()->create([
        'timestamp' => now(),
    ]);

    $orderedTransactions = Transaction::query()
        ->orderBy('timestamp', 'desc')
        ->get();

    expect($orderedTransactions->first()->id)->toBe($newestTransaction->id)
        ->and($orderedTransactions->last()->id)->toBe($oldestTransaction->id);
});

test('transaction amount is stored with correct precision', function () {
    $transaction = Transaction::factory()->create([
        'amount' => 100.55,
    ]);

    $freshTransaction = Transaction::find($transaction->id);

    expect($freshTransaction->amount)
        ->toBe('100.55')
        ->toBeNumeric()
        ->and(strlen(substr(strrchr((string)$freshTransaction->amount, "."), 1)))->toBe(2);
});

test('transaction validates required attributes', function () {
    expect(fn () => Transaction::create([]))->toThrow(Exception::class);
});

test('can get transactions within date range', function () {
    // Clear existing transactions
    Transaction::query()->delete();

    // Create transactions with specific dates
    Transaction::factory()->create(['timestamp' => now()->subDays(5)]);
    Transaction::factory()->create(['timestamp' => now()->subDays(3)]);
    Transaction::factory()->create(['timestamp' => now()->subDay()]);
    Transaction::factory()->create(['timestamp' => now()]);

    $transactions = Transaction::query()
        ->whereBetween('timestamp', [
            now()->subDays(3)->startOfDay(),
            now()->endOfDay(),
        ])
        ->get();

    expect($transactions)->toHaveCount(3);
});

test('transaction description is not empty', function () {
    $transactions = Transaction::factory()->count(5)->create();

    $transactions->each(function ($transaction) {
        expect($transaction->description)
            ->toBeString()
            ->not->toBeEmpty();
    });
});

test('transaction factory generates valid account types', function () {
    $transactions = Transaction::factory()->count(50)->create();

    $uniqueAccountTypes = $transactions->pluck('accountType')->unique()->values();

    expect($uniqueAccountTypes)
        ->each(fn ($accountType) => $accountType->toBeInstanceOf(AccountType::class))
        ->and(AccountType::values())->toEqualCanonicalizing(
            $uniqueAccountTypes->map(fn ($type) => $type->value)->all()
        );
});

test('can sum transaction amounts', function () {
    // Clear existing transactions
    Transaction::query()->delete();

    Transaction::factory()->create(['amount' => 100.50]);
    Transaction::factory()->create(['amount' => -50.25]);
    Transaction::factory()->create(['amount' => 75.75]);

    $sum = Transaction::query()->sum('amount');

    expect($sum)->toBe(126.00);
});

<?php

namespace App\Http\Controllers;

use App\Enums\AccountType;
use App\Events\NewTransaction;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Validation\Rules\Enum;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse|Response
    {
        if ($request->wantsJson()) {
            $transactions = Transaction::latest('created_at')
                ->with('user')
                ->when($request->accountType, function ($query, $accountType) {
                    $query->where('accountType', $accountType);
                })
                ->paginate(10);

            return response()->json([
                'data' => $transactions->through(fn ($transaction) => [
                    'id' => $transaction->id,
                    'user' => $transaction->user->name,
                    'amount' => number_format($transaction->amount, 2),
                    'description' => $transaction->description,
                    'accountType' => $transaction->accountType->value,
                    'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                ]),
                'meta' => [
                    'current_page' => $transactions->currentPage(),
                    'last_page' => $transactions->lastPage(),
                    'per_page' => $transactions->perPage(),
                    'total' => $transactions->total(),
                ]
            ]);
        }

        return Inertia::render('Transactions/Index', [
            'transactions' => Transaction::latest('created_at')
                ->with('user')
                ->when($request->accountType, function ($query, $accountType) {
                    $query->where('accountType', $accountType);
                })
                ->paginate(10)
                ->through(fn ($transaction) => [
                    'id' => $transaction->id,
                    'user' => $transaction->user->name,
                    'amount' => number_format($transaction->amount, 2),
                    'description' => $transaction->description,
                    'accountType' => $transaction->accountType->value,
                    'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                ]),
            'filters' => [
                'accountType' => $request->accountType,
            ],
            'accountTypes' => AccountType::values(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'description' => 'required|string|max:255',
            'accountType' => ['required', new Enum(AccountType::class)]

        ]);

        $transaction = Transaction::create([
            ...$validated,
            'user_id'   => $request->user()->id
        ]);

        $transaction->load('user');

        broadcast(new NewTransaction($transaction))->toOthers();

        return response()->json([
            'message' => 'Transaction created successfully',
            'transaction' => [
                'id' => $transaction->id,
                'amount' => number_format($transaction->amount, 2),
                'description' => $transaction->description,
                'accountType' => $transaction->accountType->value,
            ]
        ], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse|Response
    {
        $transaction = Transaction::with('user')->findOrFail($id);

        if (request()->wantsJson()) {
            return response()->json([
                'data' => [
                    'id' => $transaction->id,
                    'user' => $transaction->user->name,
                    'amount' => number_format($transaction->amount, 2),
                    'description' => $transaction->description,
                    'accountType' => $transaction->accountType->value,
                    'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $transaction->updated_at->format('Y-m-d H:i:s'),
                ]
            ]);
        }

        return Inertia::render('Transactions/Show', [
            'transaction' => [
                'id' => $transaction->id,
                'user' => $transaction->user->name,
                'amount' => number_format($transaction->amount, 2),
                'description' => $transaction->description,
                'accountType' => $transaction->accountType->value,
                'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $transaction->updated_at->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

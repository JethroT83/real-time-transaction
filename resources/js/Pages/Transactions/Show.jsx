import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Show({ auth, transaction }) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Transaction Details</h2>}
        >
            <Head title="Transaction Details" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            <div className="mb-4">
                                <Link
                                    href={route('transactions.index')}
                                    className="text-indigo-600 hover:text-indigo-900"
                                >
                                    ← Back to Transactions
                                </Link>
                            </div>

                            <dl className="grid grid-cols-2 gap-4">
                                <div>
                                    <dt className="font-semibold">Timestamp</dt>
                                    <dd>{transaction.timestamp}</dd>
                                </div>
                                <div>
                                    <dt className="font-semibold">Amount</dt>
                                    <dd>${transaction.amount}</dd>
                                </div>
                                <div>
                                    <dt className="font-semibold">Description</dt>
                                    <dd>{transaction.description}</dd>
                                </div>
                                <div>
                                    <dt className="font-semibold">Account Type</dt>
                                    <dd>{transaction.accountType}</dd>
                                </div>
                                <div>
                                    <dt className="font-semibold">Created At</dt>
                                    <dd>{transaction.created_at}</dd>
                                </div>
                                <div>
                                    <dt className="font-semibold">Updated At</dt>
                                    <dd>{transaction.updated_at}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

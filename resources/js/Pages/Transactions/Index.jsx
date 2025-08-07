import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { useState, useEffect } from 'react';
import Echo from 'laravel-echo';


export default function Index({ auth, transactions: initialTransactions, filters, accountTypes}) {
    const [transactions, setTransactions] = useState(initialTransactions);
    const [accountType, setAccountType] = useState(filters.accountType || '');

    useEffect(() => {

        window.Echo.channel('transactions')
            .listen('NewTransaction', (event) => {
                if (!accountType || event.transaction.accountType === accountType) {
                    setTransactions(prev => ({
                        ...prev,
                        data: [event.transaction, ...prev.data]
                    }));
                }
            });

        // Cleanup
        return () => {
            window.Echo.leave('transactions');
        };
    }, [accountType]);


    const handleFilterChange = (e) => {
        const value = e.target.value;
        setAccountType(value);

        router.get(route('transactions.index'), {
            accountType: value || null,
        }, {
            preserveState: true,
            preserveScroll: true,
            replace: true
        });
    };

    const [isLoading, setIsLoading] = useState(false);

    useEffect(() => {
        router.on('start', () => setIsLoading(true));
        router.on('finish', () => setIsLoading(false));
    }, []);

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Transactions</h2>}
        >
            <Head title="Transactions" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            <div className="mb-4">
                                <label htmlFor="accountType" className="block text-sm font-medium text-gray-700">
                                    Filter by Account Type
                                </label>
                                <select
                                    id="accountType"
                                    value={accountType}
                                    onChange={handleFilterChange}
                                    className="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                                >
                                    <option value="">All Accounts</option>
                                    {accountTypes.map((type) => (
                                        <option key={type} value={type}>
                                            {type.charAt(0).toUpperCase() + type.slice(1)}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            <table className="min-w-full divide-y divide-gray-200">
                                <thead className="bg-gray-50">
                                <tr>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Account Type
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        User
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Amount
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Description
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Timestamp
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                                </thead>
                                <tbody className="bg-white divide-y divide-gray-200">
                                {transactions.data.map((transaction) => (
                                    <tr key={transaction.id}>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            {transaction.accountType}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            {transaction.user}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            ${transaction.amount}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            {transaction.description}
                                        </td>

                                        <td className="px-6 py-4 whitespace-nowrap">
                                            {transaction.created_at}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <Link
                                                href={route('transactions.show', transaction.id)}
                                                className="text-indigo-600 hover:text-indigo-900"
                                            >
                                                View
                                            </Link>
                                        </td>
                                    </tr>
                                ))}
                                </tbody>
                            </table>

                            {/* Pagination */}
                            <div className="mt-4">
                                {transactions.links.map((link, index) => (
                                    link.url ? (
                                        <Link
                                            key={index}
                                            href={link.url}
                                            className={`px-3 py-2 border ${
                                                link.active ? 'bg-blue-500 text-white' : 'text-gray-700'
                                            }`}
                                            dangerouslySetInnerHTML={{ __html: link.label }}
                                        />
                                    ) : (
                                        <span
                                            key={index}
                                            className="px-3 py-2 border text-gray-400"
                                            dangerouslySetInnerHTML={{ __html: link.label }}
                                        />
                                    )
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div className="mb-4">
                {isLoading && (
                    <div className="ml-2 inline-block">
                        <span className="text-gray-500">Loading...</span>
                    </div>
                )}
            </div>

        </AuthenticatedLayout>
    );
}

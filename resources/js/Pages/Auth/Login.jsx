import React from 'react';
import { Head, useForm } from '@inertiajs/react';

export default function Login() {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('login'));
    };

    return (
        <>
            <Head title="Connexion" />
            <div className="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
                <div className="text-center mb-10">
                    <h1 className="text-4xl font-bold text-gray-900">SYNCHROGEST-EDUCATION</h1>
                    <p className="mt-2 text-gray-600">Connectez-vous pour accéder à votre module</p>
                </div>

                <div className="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                    <form onSubmit={handleSubmit}>
                        <div className="mb-4">
                            <label htmlFor="email" className="block text-gray-700 text-sm font-bold mb-2">
                                Email
                            </label>
                            <input
                                id="email"
                                type="email"
                                className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                value={data.email}
                                onChange={e => setData('email', e.target.value)}
                                required
                            />
                            {errors.email && <div className="text-red-500 text-xs mt-1">{errors.email}</div>}
                        </div>

                        <div className="mb-6">
                            <label htmlFor="password" className="block text-gray-700 text-sm font-bold mb-2">
                                Mot de passe
                            </label>
                            <input
                                id="password"
                                type="password"
                                className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                value={data.password}
                                onChange={e => setData('password', e.target.value)}
                                required
                            />
                            {errors.password && <div className="text-red-500 text-xs mt-1">{errors.password}</div>}
                        </div>

                        <div className="flex items-center justify-between mb-6">
                            <label className="flex items-center">
                                <input
                                    type="checkbox"
                                    className="form-checkbox"
                                    checked={data.remember}
                                    onChange={e => setData('remember', e.target.checked)}
                                />
                                <span className="ml-2 text-sm text-gray-600">Se souvenir de moi</span>
                            </label>
                        </div>

                        <div className="flex items-center justify-end">
                            <button
                                type="submit"
                                className="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                                disabled={processing}
                            >
                                Se connecter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </>
    );
}
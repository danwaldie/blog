import React from 'react';
import { Link } from '@inertiajs/react';

export default function BlogLayout({ children }) {
    return (
        <div className="min-h-screen bg-gray-50 font-sans text-gray-900 antialiased">
            {/* Navigation */}
            <nav className="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm">
                <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-between h-16 items-center">
                        <div className="flex items-center">
                            <Link href="/" className="text-2xl font-black tracking-tight text-indigo-600 hover:text-indigo-500 transition-colors">
                                Personal Blog
                            </Link>
                        </div>
                        <div className="hidden sm:flex sm:items-center sm:ml-6 space-x-8">
                            <Link href="/" className="text-sm font-medium text-gray-600 hover:text-indigo-600 transition-colors">
                                Home
                            </Link>
                            <a href="#" className="text-sm font-medium text-gray-600 hover:text-indigo-600 transition-colors">
                                About
                            </a>
                        </div>
                    </div>
                </div>
            </nav>

            {/* Content */}
            <main className="max-w-5xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                {children}
            </main>

            {/* Footer */}
            <footer className="bg-white border-t border-gray-100 py-12 mt-12">
                <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="text-center">
                        <p className="text-gray-400 text-sm">
                            &copy; {new Date().getFullYear()} Personal Blog. Built with Laravel, Inertia, and React.
                        </p>
                    </div>
                </div>
            </footer>
        </div>
    );
}

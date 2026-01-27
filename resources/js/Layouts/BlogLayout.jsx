import React, { useState, useEffect } from 'react';
import { Link } from '@inertiajs/react';
import ThemeToggle from '@/Components/ThemeToggle';
import { Toaster } from 'sonner';

export default function BlogLayout({ children }) {
    const [scrolled, setScrolled] = useState(false);

    useEffect(() => {
        const handleScroll = () => {
            setScrolled(window.scrollY > 20);
        };
        window.addEventListener('scroll', handleScroll);
        return () => window.removeEventListener('scroll', handleScroll);
    }, []);

    return (
        <div className="min-h-screen bg-white dark:bg-slate-900 font-sans text-slate-900 dark:text-slate-200 antialiased selection:bg-emerald-500 selection:text-white transition-colors duration-300">
            {/* Navigation */}
            <nav
                className={`fixed w-full top-0 z-50 transition-all duration-300 border-b ${scrolled
                    ? 'bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-slate-200 dark:border-slate-800'
                    : 'bg-white dark:bg-slate-900 border-transparent'
                    }`}
            >
                <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-between h-16 items-center">
                        <div className="flex items-center">
                            <Link
                                href="/"
                                className="text-xl font-bold tracking-tight text-slate-900 dark:text-white hover:text-emerald-600 dark:hover:text-emerald-500 transition-colors"
                            >
                                Dan Waldie
                            </Link>
                        </div>
                        <div className="flex items-center space-x-6">
                            <div className="hidden sm:flex space-x-6">
                                <Link
                                    href="/"
                                    className="text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-500 transition-colors"
                                >
                                    Home
                                </Link>
                                <a
                                    href="#"
                                    className="text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-500 transition-colors"
                                >
                                    About
                                </a>
                            </div>
                            <div className="pl-6 border-l border-slate-200 dark:border-slate-700">
                                <ThemeToggle />
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            {/* Content */}
            <main className="max-w-4xl mx-auto pt-32 pb-20 px-4 sm:px-6 lg:px-8 min-h-[calc(100vh-160px)]">
                {children}
            </main>

            {/* Footer */}
            <footer className="bg-slate-50 dark:bg-slate-950 border-t border-slate-200 dark:border-slate-800 py-12">
                <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex flex-col md:flex-row justify-between items-center gap-4">
                        <p className="text-slate-500 dark:text-slate-400 text-sm">
                            &copy; {new Date().getFullYear()} Dan Waldie.
                        </p>
                        <div className="flex space-x-6 text-sm text-slate-500 dark:text-slate-400">
                            <a href="#" className="hover:text-emerald-600 dark:hover:text-emerald-500 transition-colors">Twitter</a>
                            <a href="#" className="hover:text-emerald-600 dark:hover:text-emerald-500 transition-colors">GitHub</a>
                            <a href="#" className="hover:text-emerald-600 dark:hover:text-emerald-500 transition-colors">RSS</a>
                        </div>
                    </div>
                </div>
            </footer>
            <Toaster position="bottom-right" richColors theme="system" />
        </div>
    );
}

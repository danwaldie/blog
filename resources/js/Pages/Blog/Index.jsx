import React from 'react';
import { Head, Link } from '@inertiajs/react';
import BlogLayout from '@/Layouts/BlogLayout';

export default function Index({ posts }) {
    return (
        <BlogLayout>
            <Head title="Welcome to my Blog" />

            <div className="space-y-20">
                <header className="space-y-6">
                    <h1 className="text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white sm:text-5xl">
                        Writing
                    </h1>
                    <p className="text-xl text-slate-500 dark:text-slate-400 max-w-2xl leading-relaxed">
                        Thoughts on software engineering, Laravel, and building better products.
                    </p>
                </header>

                <div className="space-y-12">
                    {posts.length > 0 ? (
                        posts.map((post) => (
                            <article key={post.id} className="group relative flex flex-col items-start">
                                <Link href={route('blog.show', post.slug)} className="block w-full">
                                    <div className="flex flex-col md:flex-row md:items-baseline md:gap-8">
                                        <time className="flex-shrink-0 text-sm text-slate-400 dark:text-slate-500 font-mono mb-2 md:mb-0 w-32">
                                            {new Date(post.published_at).toLocaleDateString('en-US', {
                                                year: 'numeric',
                                                month: 'short',
                                                day: 'numeric'
                                            })}
                                        </time>

                                        <div className="flex-1 space-y-3">
                                            <h2 className="text-2xl font-bold tracking-tight text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                                {post.title}
                                            </h2>
                                            <p className="text-slate-600 dark:text-slate-400 leading-relaxed">
                                                {post.excerpt}
                                            </p>
                                            <div className="flex items-center text-sm font-medium text-emerald-600 dark:text-emerald-500 opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300">
                                                Read article
                                                <svg className="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </Link>
                            </article>
                        ))
                    ) : (
                        <div className="text-center py-24 bg-slate-50 dark:bg-slate-900/50 rounded-3xl border border-slate-200 dark:border-slate-800 border-dashed">
                            <p className="text-slate-500 dark:text-slate-400 text-lg">No posts published yet.</p>
                        </div>
                    )}
                </div>
            </div>
        </BlogLayout>
    );
}

import React from 'react';
import { Head, Link } from '@inertiajs/react';
import BlogLayout from '@/Layouts/BlogLayout';

export default function Index({ posts }) {
    return (
        <BlogLayout>
            <Head title="Welcome to my Blog" />

            <div className="space-y-16">
                <header>
                    <h1 className="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">
                        Latest Stories
                    </h1>
                    <p className="mt-4 text-xl text-gray-500 max-w-2xl">
                        Thoughts, tutorials, and snippets on software development and beyond.
                    </p>
                </header>

                <div className="grid gap-12 pt-12 border-t border-gray-100">
                    {posts.length > 0 ? (
                        posts.map((post) => (
                            <article key={post.id} className="group relative flex flex-col items-start">
                                <h2 className="text-2xl font-bold tracking-tight text-gray-900 group-hover:text-indigo-600 transition-colors">
                                    <Link href={route('blog.show', post.slug)}>
                                        <span className="absolute -inset-y-2.5 -inset-x-4 z-20 sm:-inset-x-6 sm:rounded-2xl" />
                                        <span className="relative z-10">{post.title}</span>
                                    </Link>
                                </h2>
                                <time className="relative z-10 order-first mb-3 flex items-center text-sm text-gray-400 pl-3.5" dateTime={post.published_at}>
                                    <span className="absolute inset-y-0 left-0 flex items-center" aria-hidden="true">
                                        <span className="h-4 w-0.5 rounded-full bg-gray-200" />
                                    </span>
                                    {new Date(post.published_at).toLocaleDateString('en-US', {
                                        year: 'numeric',
                                        month: 'long',
                                        day: 'numeric'
                                    })}
                                </time>
                                <p className="relative z-10 mt-2 text-base text-gray-600 leading-relaxed max-w-3xl">
                                    {post.excerpt}
                                </p>
                                <div className="relative z-10 mt-4 flex items-center text-sm font-semibold text-indigo-600 group-hover:text-indigo-500 transition-colors">
                                    Read more
                                    <svg className="ml-1 h-3 w-3 stroke-current" fill="none" viewBox="0 0 10 10" aria-hidden="true">
                                        <path d="M0 5h7M4 1l4 4-4 4" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                    </svg>
                                </div>
                            </article>
                        ))
                    ) : (
                        <div className="text-center py-20 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                            <p className="text-gray-500 text-lg">No posts published yet. Stay tuned!</p>
                        </div>
                    )}
                </div>
            </div>
        </BlogLayout>
    );
}

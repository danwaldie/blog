import React from 'react';
import { Head, Link } from '@inertiajs/react';
import BlogLayout from '@/Layouts/BlogLayout';

export default function Show({ post }) {
    return (
        <BlogLayout>
            <Head title={post.title} />

            <article className="max-w-3xl mx-auto">
                <header className="mb-12">
                    <Link
                        href="/"
                        className="inline-flex items-center text-sm font-medium text-gray-500 hover:text-indigo-600 transition-colors mb-8"
                    >
                        <svg className="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to all posts
                    </Link>

                    <h1 className="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl mb-6">
                        {post.title}
                    </h1>

                    <div className="flex items-center space-x-4 border-b border-gray-100 pb-8">
                        {post.author && (
                            <div className="flex items-center">
                                <div className="h-10 w-10 flex-shrink-0 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold">
                                    {post.author.name.charAt(0)}
                                </div>
                                <div className="ml-3">
                                    <p className="text-sm font-semibold text-gray-900">{post.author.name}</p>
                                    <time className="text-xs text-gray-500" dateTime={post.published_at}>
                                        {new Date(post.published_at).toLocaleDateString('en-US', {
                                            year: 'numeric',
                                            month: 'long',
                                            day: 'numeric'
                                        })}
                                    </time>
                                </div>
                            </div>
                        )}

                        <div className="flex-1"></div>

                        {post.tags && post.tags.length > 0 && (
                            <div className="flex flex-wrap gap-2">
                                {post.tags.map(tag => (
                                    <span key={tag.id} className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        #{tag.name}
                                    </span>
                                ))}
                            </div>
                        )}
                    </div>
                </header>

                <div
                    className="prose prose-indigo prose-lg max-w-none text-gray-700 leading-relaxed"
                    dangerouslySetInnerHTML={{ __html: post.body_html }}
                />
            </article>
        </BlogLayout>
    );
}

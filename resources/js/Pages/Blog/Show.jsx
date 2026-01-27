import React, { useEffect, useRef, useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import BlogLayout from '@/Layouts/BlogLayout';
import CommentForm from '@/Components/CommentForm';
import CommentList from '@/Components/CommentList';
import hljs from 'highlight.js';
import 'highlight.js/styles/github-dark.css';

export default function Show({ post }) {
    const [showCommentForm, setShowCommentForm] = useState(false);
    const commentFormRef = useRef(null);

    useEffect(() => {
        hljs.highlightAll();
    }, [post.body_html]);

    const scrollToCommentForm = () => {
        setShowCommentForm(true);
        setTimeout(() => {
            commentFormRef.current?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 100);
    };

    return (
        <BlogLayout>
            <Head title={post.title} />

            <article className="max-w-3xl mx-auto">
                <header className="mb-12 text-center">
                    <Link
                        href="/"
                        className="inline-flex items-center text-sm font-medium text-emerald-600 dark:text-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors mb-8"
                    >
                        <svg className="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to all posts
                    </Link>

                    <h1 className="text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white sm:text-5xl mb-6">
                        {post.title}
                    </h1>

                    <div className="flex items-center justify-center space-x-2 text-slate-500 dark:text-slate-400">
                        <time dateTime={post.published_at}>
                            {new Date(post.published_at).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            })}
                        </time>
                        <span>•</span>
                        <span>{post.author ? post.author.name : 'Unknown Author'}</span>
                    </div>

                    {post.tags && post.tags.length > 0 && (
                        <div className="flex flex-wrap justify-center gap-2 mt-6">
                            {post.tags.map(tag => (
                                <span key={tag.id} className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                    #{tag.name}
                                </span>
                            ))}
                        </div>
                    )}

                    <div className="mt-8 flex justify-center">
                        <button
                            onClick={scrollToCommentForm}
                            className="inline-flex items-center text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-500 transition-colors"
                        >
                            <svg className="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            {post.comments?.length || 0} {post.comments?.length === 1 ? 'Comment' : 'Comments'}
                            <span className="mx-2">•</span>
                            Leave a comment
                        </button>
                    </div>
                </header>

                <div
                    className="prose prose-lg prose-slate dark:prose-invert max-w-none 
                        prose-headings:font-bold prose-headings:tracking-tight 
                        prose-a:text-emerald-600 dark:prose-a:text-emerald-500 hover:prose-a:text-emerald-500 
                        prose-img:rounded-2xl prose-img:shadow-lg
                        prose-pre:bg-slate-900 prose-pre:shadow-lg dark:prose-pre:bg-slate-950 dark:prose-pre:border dark:prose-pre:border-slate-800"
                    dangerouslySetInnerHTML={{ __html: post.body_html }}
                />

                <hr className="my-16 border-slate-100 dark:border-slate-800" />

                <section id="comments" className="space-y-16">
                    <CommentList comments={post.comments} />

                    <div ref={commentFormRef} className="pt-8 border-t border-slate-100 dark:border-slate-800">
                        {!showCommentForm ? (
                            <div className="text-center">
                                <button
                                    onClick={() => setShowCommentForm(true)}
                                    className="inline-flex justify-center rounded-full border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 py-3 px-8 text-sm font-semibold text-slate-900 dark:text-white shadow-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-all duration-200"
                                >
                                    Leave a Comment
                                </button>
                            </div>
                        ) : (
                            <div className="space-y-8">
                                <h3 className="text-lg font-bold text-slate-900 dark:text-white">
                                    Leave a Comment
                                </h3>
                                <CommentForm
                                    postSlug={post.slug}
                                    onSuccess={() => setShowCommentForm(false)}
                                />
                            </div>
                        )}
                    </div>
                </section>
            </article>
        </BlogLayout>
    );
}

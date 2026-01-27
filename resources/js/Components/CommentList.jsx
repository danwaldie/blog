import React from 'react';

export default function CommentList({ comments = [] }) {
    if (comments.length === 0) {
        return (
            <div className="text-center py-12 bg-slate-50 dark:bg-slate-900/50 rounded-3xl border border-slate-200 dark:border-slate-800 border-dashed">
                <p className="text-slate-500 dark:text-slate-400">No comments yet. Be the first to share your thoughts!</p>
            </div>
        );
    }

    return (
        <div className="space-y-10">
            <div className="flex items-center space-x-3">
                <h3 className="text-lg font-bold text-slate-900 dark:text-white">
                    Comments
                </h3>
                <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-400">
                    {comments.length}
                </span>
            </div>

            <ul className="divide-y divide-slate-100 dark:divide-slate-800/50">
                {comments.map((comment) => (
                    <li key={comment.id} className="py-8 first:pt-0 last:pb-0">
                        <div className="flex space-x-4">
                            <div className="flex-shrink-0">
                                <div className="h-10 w-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 font-bold border border-slate-200 dark:border-slate-700/50">
                                    {comment.commenter_name.charAt(0).toUpperCase()}
                                </div>
                            </div>
                            <div className="flex-1 min-w-0 space-y-2">
                                <div className="flex items-center justify-between">
                                    <h4 className="text-sm font-bold text-slate-900 dark:text-white">
                                        {comment.commenter_name}
                                    </h4>
                                    <time className="text-xs text-slate-400 dark:text-slate-500 font-mono">
                                        {new Date(comment.published_at || comment.created_at).toLocaleDateString('en-US', {
                                            month: 'short',
                                            day: 'numeric',
                                            year: 'numeric',
                                            hour: '2-digit',
                                            minute: '2-digit'
                                        })}
                                    </time>
                                </div>
                                <div className="text-slate-600 dark:text-slate-400 leading-relaxed text-[15px] break-words">
                                    {comment.body}
                                </div>
                            </div>
                        </div>
                    </li>
                ))}
            </ul>
        </div>
    );
}

import React from 'react';
import { useForm } from '@inertiajs/react';
import { toast } from 'sonner';

export default function CommentForm({ postSlug, onSuccess }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        commenter_name: '',
        body: '',
    });

    const NAME_LIMIT = 50;
    const BODY_LIMIT = 2000;

    const bodyLength = data.body.length;
    const isBodyNearLimit = bodyLength > BODY_LIMIT * 0.9;
    const isBodyAtLimit = bodyLength >= BODY_LIMIT;

    const submit = (e) => {
        e.preventDefault();

        // Basic client-side validation for whitespace
        if (!data.commenter_name.trim() || !data.body.trim()) {
            return;
        }

        if (bodyLength > BODY_LIMIT || data.commenter_name.length > NAME_LIMIT) {
            return;
        }

        post(`/posts/${postSlug}/comments`, {
            onSuccess: () => {
                reset('body');
                toast.success('Your comment has been submitted! It will appear after moderation.');
                if (onSuccess) onSuccess();
            },
            preserveScroll: true,
        });
    };

    return (
        <form onSubmit={submit} className="space-y-6">
            <div>
                <div className="flex justify-between items-center">
                    <label htmlFor="commenter_name" className="block text-sm font-medium text-slate-700 dark:text-slate-300">
                        Name
                    </label>
                    <span className={`text-[10px] ${data.commenter_name.length > NAME_LIMIT ? 'text-red-500' : 'text-slate-400'}`}>
                        {data.commenter_name.length}/{NAME_LIMIT}
                    </span>
                </div>
                <input
                    type="text"
                    id="commenter_name"
                    value={data.commenter_name}
                    onChange={(e) => setData('commenter_name', e.target.value)}
                    maxLength={NAME_LIMIT + 10} // Allow a little over for validation visibility
                    className="mt-1 block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:border-emerald-500 focus:ring-emerald-500 transition-colors sm:text-sm shadow-sm"
                    placeholder="Your name"
                    required
                />
                {errors.commenter_name && <p className="mt-2 text-sm text-red-600 dark:text-red-400">{errors.commenter_name}</p>}
            </div>

            <div>
                <div className="flex justify-between items-center">
                    <label htmlFor="body" className="block text-sm font-medium text-slate-700 dark:text-slate-300">
                        Comment
                    </label>
                    <span className={`text-[10px] font-medium transition-colors duration-200 ${isBodyAtLimit ? 'text-red-500' :
                            isBodyNearLimit ? 'text-orange-500' :
                                'text-slate-400'
                        }`}>
                        {bodyLength}/{BODY_LIMIT}
                    </span>
                </div>
                <textarea
                    id="body"
                    rows={4}
                    value={data.body}
                    onChange={(e) => setData('body', e.target.value)}
                    maxLength={BODY_LIMIT + 50} // Allow a little over for validation visibility
                    className={`mt-1 block w-full rounded-xl bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-all duration-200 sm:text-sm shadow-sm ${isBodyAtLimit ? 'border-red-500 focus:ring-red-500 focus:border-red-500' :
                            isBodyNearLimit ? 'border-orange-400 focus:ring-orange-400 focus:border-orange-400' :
                                'border-slate-200 dark:border-slate-800 focus:border-emerald-500 focus:ring-emerald-500'
                        }`}
                    placeholder="What's on your mind?"
                    required
                />
                {errors.body && <p className="mt-2 text-sm text-red-600 dark:text-red-400">{errors.body}</p>}
                {isBodyAtLimit && <p className="mt-1 text-[11px] text-red-500">Maximum character limit reached.</p>}
            </div>

            <div>
                <button
                    type="submit"
                    disabled={processing || isBodyAtLimit || data.commenter_name.length > NAME_LIMIT}
                    className="inline-flex justify-center rounded-full border border-transparent bg-emerald-600 py-2.5 px-6 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:opacity-50 transition-all duration-200"
                >
                    {processing ? 'Posting...' : 'Post Comment'}
                </button>
            </div>
        </form>
    );
}

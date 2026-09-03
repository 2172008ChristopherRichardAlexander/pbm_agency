import type { FormEvent, FormHTMLAttributes, ReactNode } from 'react';
import { useState } from 'react';
import { useFormTracking } from '@/hooks/use-form-tracking';

export type LeadResponse = { lead_id: number; redirect_url: string };

type Props = Omit<FormHTMLAttributes<HTMLFormElement>, 'onSubmit' | 'onInput'> & {
    formName: string;
    children: ReactNode;
    endpoint?: string;
    onSuccess?: (response: LeadResponse) => void;
    onError?: (message: string) => void;
};

export function TrackedForm({ formName, endpoint = '/lead', onSuccess, onError, children, ...props }: Props) {
    const { onInput } = useFormTracking(formName);
    const [submitting, setSubmitting] = useState(false);

    const submit = async (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        if (submitting) return;
        setSubmitting(true);

        const form = new FormData(event.currentTarget);
        const payload = Object.fromEntries(form.entries());
        const csrf = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '';

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
                body: JSON.stringify({ ...payload, form_name: formName }),
            });
            const body = await response.json();
            if (!response.ok) throw new Error(body.message ?? 'Form submission failed.');
            onSuccess?.(body as LeadResponse);
        } catch (error) {
            onError?.(error instanceof Error ? error.message : 'Form submission failed.');
        } finally {
            setSubmitting(false);
        }
    };

    return <form {...props} aria-busy={submitting} onInput={onInput} onSubmit={submit}>{children}</form>;
}

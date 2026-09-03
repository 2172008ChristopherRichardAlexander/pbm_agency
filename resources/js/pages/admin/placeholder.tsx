import { Head } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';

export default function AdminPlaceholder() {
    return (
        <AdminLayout>
            <Head title="Admin" />
            <div className="p-6">
                <h1 className="text-2xl font-semibold">PBM Admin</h1>
                <p className="mt-2 text-muted-foreground">Analytics dashboard is being configured.</p>
            </div>
        </AdminLayout>
    );
}

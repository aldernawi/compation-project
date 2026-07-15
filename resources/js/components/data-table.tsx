import { Link } from '@inertiajs/react';
import type { ReactNode } from 'react';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { cn } from '@/lib/utils';
import type { LaravelPaginator } from '@/types';

export type DataTableColumn<T> = {
    header: string;
    cell: (row: T) => ReactNode;
    className?: string;
};

export function DataTable<T extends { id: number | string }>({
    columns,
    paginator,
}: {
    columns: DataTableColumn<T>[];
    paginator: LaravelPaginator<T>;
}) {
    return (
        <div className="flex flex-col gap-4">
            <Table>
                <TableHeader>
                    <TableRow>
                        {columns.map((column) => (
                            <TableHead key={column.header} className={column.className}>
                                {column.header}
                            </TableHead>
                        ))}
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {paginator.data.length === 0 ? (
                        <TableRow>
                            <TableCell colSpan={columns.length} className="text-muted-foreground text-center">
                                No results.
                            </TableCell>
                        </TableRow>
                    ) : (
                        paginator.data.map((row) => (
                            <TableRow key={row.id}>
                                {columns.map((column) => (
                                    <TableCell key={column.header} className={column.className}>
                                        {column.cell(row)}
                                    </TableCell>
                                ))}
                            </TableRow>
                        ))
                    )}
                </TableBody>
            </Table>

            {paginator.links.length > 3 && (
                <nav className="flex flex-wrap items-center gap-1">
                    {paginator.links.map((link, index) => (
                        <Link
                            key={index}
                            href={link.url ?? '#'}
                            preserveScroll
                            className={cn(
                                'rounded-md px-3 py-1.5 text-sm',
                                link.active ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-muted',
                                !link.url && 'pointer-events-none opacity-50',
                            )}
                            dangerouslySetInnerHTML={{ __html: link.label }}
                        />
                    ))}
                </nav>
            )}
        </div>
    );
}

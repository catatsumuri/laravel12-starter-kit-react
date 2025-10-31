import DeleteAvatar from '@/components/delete-avatar';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { useInitials } from '@/hooks/use-initials';
import { cn } from '@/lib/utils';
import { FileImage, Upload, X } from 'lucide-react';
import { forwardRef, useImperativeHandle, useRef, useState } from 'react';

interface AvatarUploadProps {
    currentAvatar?: string;
    userName: string;
    onFileSelect: (file: File | null) => void;
    error?: string;
}

export interface AvatarUploadRef {
    clearSelection: () => void;
}

const AvatarUpload = forwardRef<AvatarUploadRef, AvatarUploadProps>(
    ({ currentAvatar, userName, onFileSelect, error }, ref) => {
        const [selectedFile, setSelectedFile] = useState<File | null>(null);
        const [preview, setPreview] = useState<string | null>(null);
        const [isDragging, setIsDragging] = useState(false);
        const fileInputRef = useRef<HTMLInputElement>(null);
        const getInitials = useInitials();

        useImperativeHandle(ref, () => ({
            clearSelection: () => {
                setSelectedFile(null);
                setPreview(null);
                onFileSelect(null);
                if (fileInputRef.current) {
                    fileInputRef.current.value = '';
                }
            },
        }));

        const handleFileChange = (file: File | null) => {
            if (!file) {
                setSelectedFile(null);
                setPreview(null);
                onFileSelect(null);
                return;
            }

            // Validate file type
            if (!file.type.startsWith('image/')) {
                return;
            }

            // Validate file size (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                return;
            }

            // Create preview
            const reader = new FileReader();
            reader.onloadend = () => {
                setPreview(reader.result as string);
            };
            reader.readAsDataURL(file);

            setSelectedFile(file);
            onFileSelect(file);
        };

        const handleDragOver = (e: React.DragEvent) => {
            e.preventDefault();
            setIsDragging(true);
        };

        const handleDragLeave = (e: React.DragEvent) => {
            e.preventDefault();
            setIsDragging(false);
        };

        const handleDrop = (e: React.DragEvent) => {
            e.preventDefault();
            setIsDragging(false);

            const file = e.dataTransfer.files[0];
            if (file) {
                handleFileChange(file);
            }
        };

        const handleRemove = () => {
            setSelectedFile(null);
            setPreview(null);
            onFileSelect(null);
            if (fileInputRef.current) {
                fileInputRef.current.value = '';
            }
        };

        const formatFileSize = (bytes: number): string => {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
        };

        const userInitials = getInitials(userName);

        return (
            <div className="space-y-4">
                <Label>Profile picture</Label>

                <div className="flex items-start gap-6">
                    {/* Avatar preview - shows current avatar only */}
                    <div className="flex flex-col items-center gap-3">
                        <Avatar className="size-24">
                            {currentAvatar && (
                                <AvatarImage
                                    src={currentAvatar}
                                    alt={userName}
                                />
                            )}
                            <AvatarFallback className="text-lg">
                                {userInitials}
                            </AvatarFallback>
                        </Avatar>
                        {currentAvatar && <DeleteAvatar hasAvatar={true} />}
                    </div>

                    {/* Upload area */}
                    <div className="flex-1 space-y-3">
                        {!selectedFile ? (
                            <div
                                onDragOver={handleDragOver}
                                onDragLeave={handleDragLeave}
                                onDrop={handleDrop}
                                className={cn(
                                    'relative flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed px-6 py-8 transition-colors',
                                    isDragging
                                        ? 'border-primary bg-primary/5'
                                        : 'border-muted-foreground/25 hover:border-muted-foreground/50',
                                    error && 'border-destructive',
                                )}
                                onClick={() => fileInputRef.current?.click()}
                            >
                                <Upload className="mb-2 size-8 text-muted-foreground" />
                                <p className="text-sm font-medium">
                                    Click to upload or drag and drop
                                </p>
                                <p className="mt-1 text-xs text-muted-foreground">
                                    PNG, JPG, GIF up to 5MB
                                </p>

                                <input
                                    ref={fileInputRef}
                                    type="file"
                                    accept="image/*"
                                    onChange={(e) =>
                                        handleFileChange(
                                            e.target.files?.[0] || null,
                                        )
                                    }
                                    className="hidden"
                                />
                            </div>
                        ) : (
                            <div
                                onDragOver={handleDragOver}
                                onDragLeave={handleDragLeave}
                                onDrop={handleDrop}
                                className={cn(
                                    'rounded-lg border bg-muted/30 px-6 py-4 transition-colors',
                                    isDragging
                                        ? 'border-primary bg-primary/5'
                                        : 'border-muted-foreground/25',
                                )}
                            >
                                <div className="flex items-start gap-4">
                                    {preview ? (
                                        <div className="size-16 shrink-0 overflow-hidden rounded-md border border-muted-foreground/25">
                                            <img
                                                src={preview}
                                                alt="Preview"
                                                className="size-full object-cover"
                                            />
                                        </div>
                                    ) : (
                                        <div className="rounded-md bg-primary/10 p-3">
                                            <FileImage className="size-6 text-primary" />
                                        </div>
                                    )}
                                    <div className="min-w-0 flex-1">
                                        <p className="truncate text-sm font-medium">
                                            {selectedFile.name}
                                        </p>
                                        <p className="mt-1 text-xs text-muted-foreground">
                                            {formatFileSize(selectedFile.size)}{' '}
                                            • Waiting to upload
                                        </p>
                                        <p className="mt-1 text-xs text-muted-foreground/75">
                                            Drop a new file to replace
                                        </p>
                                    </div>
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        size="sm"
                                        onClick={handleRemove}
                                        className="shrink-0"
                                    >
                                        <X className="size-4" />
                                    </Button>
                                </div>
                            </div>
                        )}

                        {error && (
                            <p className="text-sm font-medium text-destructive">
                                {error}
                            </p>
                        )}
                    </div>
                </div>
            </div>
        );
    },
);

AvatarUpload.displayName = 'AvatarUpload';

export default AvatarUpload;

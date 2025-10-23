import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { useInitials } from '@/hooks/use-initials';
import { cn } from '@/lib/utils';
import { Upload, X } from 'lucide-react';
import { useRef, useState } from 'react';

interface AvatarUploadProps {
    currentAvatar?: string;
    userName: string;
    onFileSelect: (file: File | null) => void;
    error?: string;
}

export default function AvatarUpload({
    currentAvatar,
    userName,
    onFileSelect,
    error,
}: AvatarUploadProps) {
    const [preview, setPreview] = useState<string | null>(null);
    const [isDragging, setIsDragging] = useState(false);
    const fileInputRef = useRef<HTMLInputElement>(null);
    const getInitials = useInitials();

    const handleFileChange = (file: File | null) => {
        if (!file) {
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
        setPreview(null);
        onFileSelect(null);
        if (fileInputRef.current) {
            fileInputRef.current.value = '';
        }
    };

    const displayAvatar = preview || currentAvatar;
    const userInitials = getInitials(userName);

    return (
        <div className="space-y-4">
            <Label>Profile picture</Label>

            <div className="flex items-start gap-6">
                {/* Avatar preview */}
                <Avatar className="size-24">
                    {displayAvatar && (
                        <AvatarImage src={displayAvatar} alt={userName} />
                    )}
                    <AvatarFallback className="text-lg">
                        {userInitials}
                    </AvatarFallback>
                </Avatar>

                {/* Upload area */}
                <div className="flex-1 space-y-3">
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
                                handleFileChange(e.target.files?.[0] || null)
                            }
                            className="hidden"
                        />
                    </div>

                    {error && (
                        <p className="text-sm font-medium text-destructive">
                            {error}
                        </p>
                    )}

                    {preview && (
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            onClick={handleRemove}
                            className="w-full"
                        >
                            <X className="mr-2 size-4" />
                            Remove
                        </Button>
                    )}
                </div>
            </div>
        </div>
    );
}

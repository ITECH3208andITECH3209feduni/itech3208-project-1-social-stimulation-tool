import { userApi } from "@/api";
import { useMutation, useQueryClient } from "@tanstack/react-query";
import { toaster } from "@/components/ui/toaster";
import loggerUtil from "@/utils/logger.utils";
import userFormRequest from "@/utils/buildUserFormRequest";

/**
 * Custom hook to handle avatar image uploads using React Query.
 * This hook implements Optimistic Updates to provide instant UI feedback
 * while the image is being uploaded in the background.
 *
 * @param {Function} setPreview - Optional state setter from the component to update local image preview.
 * @returns {Object} { uploadAvatar, isUploading }
 */
const useAvatarUpload = (setPreview) => {
    const queryClient = useQueryClient();

    const mutation = useMutation({
        // 1. The actual API call to upload the avatar
        mutationFn: async (formData) => {
            const res = await userApi.uploadAvatar(formData);
            if (!res.success) {
                throw new Error(res.message || "Failed to upload avatar");
            }
            return res.data;
        },
        // 2. onMutate fires IMMEDIATELY when mutate() is called, before the API request finishes
        onMutate: async () => {
            // Cancel any outgoing refetches to prevent them from overwriting our optimistic update
            await queryClient.cancelQueries({ queryKey: ["user-profile"] });

            // Snapshot the previous user data so we can rollback if the upload fails
            const previousUserData = queryClient.getQueryData(["user-profile"]);
            
            return { previousUserData }; // Pass this to the context for onError
        },
        // 3. onError fires if the API request throws an error
        onError: (error, _, context) => {
            // Rollback to the previous user data to undo the optimistic update
            if (context?.previousUserData) {
                queryClient.setQueryData(["user-profile"], context.previousUserData);
                if (setPreview) setPreview(context.previousUserData?.avatar?.url);
            }
            // Show error notification to the user
            toaster.create({ description: error.message, type: "error" });
            loggerUtil.error(error.message);
        },
        // 4. onSuccess fires if the API request succeeds
        onSuccess: (data) => {
            // Update the local preview with the real URL from the server
            if (setPreview) setPreview(data.avatar.url);
        },
        // 5. onSettled fires at the end, regardless of success or failure
        onSettled: () => {
            // Remove the 'isUploading' flag from the cache to hide loading spinners
            queryClient.setQueryData(["user-profile"], (old) => (old ? { ...old, isUploading: false } : old));
            // Invalidate the query to trigger a background refetch for the freshest data
            queryClient.invalidateQueries({ queryKey: ["user-profile"] });
        },
    });

    /**
     * Main function exposed to the UI to trigger the upload process.
     * @param {File} file - The image file selected by the user.
     */
    const uploadAvatar = (file) => {
        if (!file) return;

        // Create a temporary local URL for instant preview
        const imageUrl = URL.createObjectURL(file);
        if (setPreview) setPreview(imageUrl);

        // Optimistically update the React Query Cache so ALL components listening to 'user-profile'
        // (like the UserMenu header) update instantly and show a loading state.
        queryClient.setQueryData(["user-profile"], (oldData) => {
            if (!oldData) return oldData;
            return {
                ...oldData,
                isUploading: true, // Flag used by UI to show Spinners
                avatar: { ...oldData.avatar, url: imageUrl },
            };
        });

        // Build the multipart/form-data payload and trigger the mutation
        const avatarFormData = userFormRequest.buildAvatarFormData({ avatar: file });
        mutation.mutate(avatarFormData);
    };

    return { uploadAvatar, isUploading: mutation.isPending };
};

export default useAvatarUpload;

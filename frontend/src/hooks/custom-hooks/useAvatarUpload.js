import { userApi } from "@/api";

const useAvatarUpload = () => {
    const avatarUpload = async (formData, callback = {}) => {
        try {
            const res = await userApi.uploadAvatar(formData);
            if (res.success) {
                callback.onSuccess?.(res.data, res.message);
            }
        } catch (error) {
            callback.onError?.(error.message);
        } finally {
            callback.onFinally?.();
        }
    };

    return { avatarUpload };
};

export default useAvatarUpload;

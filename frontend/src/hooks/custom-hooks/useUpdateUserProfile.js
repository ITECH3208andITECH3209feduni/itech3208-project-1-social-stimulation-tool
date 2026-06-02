import { userApi } from "@/api";

const useUpdateUserProfile = () => {
    const updateProfile = async (payload, callback = {}) => {
        try {
            const res = await userApi.updateUserProfile(payload);
            if (res.success) {
                callback.onSuccess?.(res.data, res.message);
            }
        } catch (error) {
            callback.onError?.(error.message);
        } finally {
            callback.onFinally?.();
        }
    };

    return { updateProfile };
};

export default useUpdateUserProfile;

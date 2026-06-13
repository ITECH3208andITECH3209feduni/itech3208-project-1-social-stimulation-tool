import { videoApi } from "@/api";
import { useState } from "react";

const useCreateVideo = () => {
    const [loading, setLoading] = useState(false);

    const createVideo = async (payload, callback) => {
        try {
            setLoading(true);
            const res = await videoApi.createVideo(payload);
            if (res.success) {
                callback.onSuccess?.(res.data, res.message);
                setLoading(false);
            }
        } catch (error) {
            callback.onError?.(error.message);
            setLoading(false);
        } finally {
            callback.onFinally?.();
            setLoading(false);
        }
    };

    return { loading, createVideo };
};

export default useCreateVideo;

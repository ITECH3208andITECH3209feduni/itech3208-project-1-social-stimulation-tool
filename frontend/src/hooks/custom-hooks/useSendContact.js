import { contactApi } from "@/api";
import { useState } from "react";

const useSendContact = () => {
    const [loading, setLoading] = useState(false);

    const sendContact = async (payload, callback = {}) => {
        try {
            setLoading(true)
            const res = await contactApi.sendContact(payload);
            if (res.success) {
                setLoading(false);
                callback.onSuccess?.(res.data, res.message);
                return true;
            }
        } catch (error) {
            setLoading(false);
            callback.onError?.(error.message);
        } finally {
            setLoading(false);
            callback.onFinally?.();
        }
    };

    return { sendContact, loading };
};

export default useSendContact;

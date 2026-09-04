import { feedbackApi } from "@/api";
import { useEffect, useState } from "react";

const useGetFeedback = () => {
    const [feedbacks, setFeedbacks] = useState([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    useEffect(() => {
        const fetchFeedbacks = async () => {
            try {
                setLoading(true);
                const response = await feedbackApi.getTopFeedbacks();

                if (response.success) {
                    setFeedbacks(response.data);
                }
            } catch (error) {
                setError(error);
                console.error("Failed to fetch feedbacks:", {
                    error,
                    code: error?.code,
                    message: error?.message,
                    status: error?.status,
                });
            } finally {
                setLoading(false);
            }
        };

        fetchFeedbacks();
    }, []);

    return { feedbacks, loading, error };
};

export default useGetFeedback;

import { feedbackApi } from "@/api";
import { useEffect, useState } from "react";

const useGetFeedback = () => {
    const [feedbacks, setFeedbacks] = useState([]);

    useEffect(() => {
        const fetchFeedbacks = async () => {
            try {
                const response = await feedbackApi.getTopFeedbacks();
                
                if (response.success) {
                    setFeedbacks(response.data);
                }
            } catch (error) {
                console.error("Failed to fetch feedbacks:", error);
            }
        };

        fetchFeedbacks();
    }, []);

    return feedbacks;
}

export default useGetFeedback;
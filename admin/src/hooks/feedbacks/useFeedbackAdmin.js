import { keepPreviousData, useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { useSearchParams } from "react-router-dom";
import { feedbackQueries } from "@/api/feedbacks/feedback.queries";
import feedbackApi from "@/api/feedbacks/feedback.api";
import { toaster } from "@/components/ui/toaster";

const useFeedbackAdmin = () => {
    const queryClient = useQueryClient();
    const [searchParams, setSearchParams] = useSearchParams();

    const page = Number(searchParams.get("page")) || 1;
    const limit = Number(searchParams.get("limit")) || 9;
    const pinnedFilter = searchParams.get("isPinned"); // "true" | "false" | null
    const rating = Number(searchParams.get("rating")) || 0;

    // Convert string param to boolean or undefined for the API
    const isPinned =
        pinnedFilter === "true" ? true : pinnedFilter === "false" ? false : undefined;

    // --- URL setters ---
    const setPage = (newPage) => {
        setSearchParams((prev) => { prev.set("page", String(newPage)); return prev; });
    };

    const setLimit = (newLimit) => {
        setSearchParams((prev) => {
            prev.set("limit", String(newLimit));
            prev.set("page", "1");
            return prev;
        });
    };

    const setIsPinned = (value) => {
        setSearchParams((prev) => {
            if (value === undefined || value === null) {
                prev.delete("isPinned");
            } else {
                prev.set("isPinned", String(value));
            }
            prev.set("page", "1");
            return prev;
        });
    };

    const setRating = (value) => {
        setSearchParams((prev) => {
            prev.set("rating", String(value));
            prev.set("page", "1");
            return prev;
        });
    }

    // --- Query ---
    const { data, isLoading } = useQuery({
        ...feedbackQueries.list({ page, limit, isPinned, rating }),
        placeholderData: keepPreviousData,
    });

    // --- Toggle pin mutation ---
    const togglePinMutation = useMutation({
        mutationFn: (id) => feedbackApi.togglePin(id),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ["feedbacks"] });
            toaster.create({ description: "Feedback pin status updated.", type: "success" });
        },
        onError: (err) => {
            toaster.create({ description: err.message || "Failed to update.", type: "error" });
        },
    });

    // --- Delete mutation ---
    const deleteMutation = useMutation({
        mutationFn: (id) => feedbackApi.deleteFeedback(id),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ["feedbacks"] });
            toaster.create({ description: "Feedback deleted.", type: "success" });
        },
        onError: (err) => {
            toaster.create({ description: err.message || "Failed to delete.", type: "error" });
        },
    });

    return {
        feedbacks: data?.feedbacks || [],
        pagination: data?.pagination || { total: 0, page: 1, limit, totalPages: 0 },
        isLoading,
        page,
        setPage,
        limit,
        setLimit,
        isPinned,
        setIsPinned,
        rating,
        setRating,
        togglePin: (id) => togglePinMutation.mutate(id),
        deleteFeedback: (id) => deleteMutation.mutate(id),
        isTogglingPin: togglePinMutation.isPending,
        isDeleting: deleteMutation.isPending,
    };
};

export default useFeedbackAdmin;

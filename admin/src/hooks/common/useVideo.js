import { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { toaster } from "@/components/ui/toaster";
import { videoQueries } from "@/api/videos/video.queries";
import { useSearchParams } from "react-router-dom";

const useVideo = ({ initialLimit = 6 } = {}) => {
    const [searchParams, setSearchParams] = useSearchParams();

    const page = Number(searchParams.get("page")) || 1;
    const limit = Number(searchParams.get("limit")) || initialLimit;
    const status = searchParams.get("status") || "";
    const categoryId = searchParams.get("categoryId") || "";

    const setPage = (newPage) => {
        setSearchParams((prev) => {
            prev.set("page", String(newPage));
            return prev; // keep other params
        });
    };

    const setLimit = (newLimit) => {
        setSearchParams((prev) => {
            prev.set("limit", String(newLimit));
            prev.set("page", "1"); // reset page when limit change
            return prev;
        });
    };

    const setStatus = (newStatus) => {
        setSearchParams((prev) => {
            if (newStatus) {
                prev.set("status", newStatus);
            } else {
                prev.delete("status"); // Delete params if it empty → URL more clean
            }
            prev.set("page", "1");
            return prev;
        });
    };

    const setCategoryId = (newCategoryId) => {
        setSearchParams((prev) => {
            if (newCategoryId) {
                prev.set("categoryId", newCategoryId);
            } else {
                prev.delete("categoryId");
            }
            prev.set("page", "1");
            return prev;
        });
    };

    const { data, isLoading, isError, error } = useQuery(
        videoQueries.list({ page, limit, status, categoryId }),
    );

    if (isError) {
        toaster.create({
            description: error.message,
            type: "error",
        });
    }

    return {
        page,
        setPage,
        limit,
        setLimit,
        status,
        setStatus,
        categoryId,
        setCategoryId,
        videos: data?.videos || [],
        pagination: data?.pagination || { total: 0, page: 1, limit, totalPages: 0 },
        isLoading,
    };
};

export default useVideo;

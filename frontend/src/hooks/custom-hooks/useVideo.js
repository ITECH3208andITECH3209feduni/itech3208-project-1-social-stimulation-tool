import { keepPreviousData, useQuery } from "@tanstack/react-query";
import { toaster } from "@/components/ui/toaster";
import { videoQueries } from "@/api/videos/video.queries";
import { useSearchParams } from "react-router-dom";

const useVideo = ({ initialLimit = 6 } = {}) => {
    const [searchParams, setSearchParams] = useSearchParams();

    // React Router does not queue multiple setSearchParams callbacks like React state.
    // Always work on a copy and make a single update for changes affecting several filters.
    const updateSearchParams = (update) => {
        setSearchParams((currentParams) => {
            const nextParams = new URLSearchParams(currentParams);
            update(nextParams);
            return nextParams;
        });
    };

    const page = Number(searchParams.get("page")) || 1;
    const limit = Number(searchParams.get("limit")) || initialLimit;
    const status = searchParams.get("status") || "";
    const categoryId = searchParams.get("categoryId") || "";
    const subCategoryId = searchParams.get("subCategoryId") || "";

    const setPage = (newPage) => {
        updateSearchParams((params) => {
            params.set("page", String(newPage));
        });
    };

    const setLimit = (newLimit) => {
        updateSearchParams((params) => {
            params.set("limit", String(newLimit));
            params.set("page", "1");
        });
    };

    const setStatus = (newStatus) => {
        updateSearchParams((params) => {
            if (newStatus) {
                params.set("status", newStatus);
            } else {
                params.delete("status");
            }
            params.set("page", "1");
        });
    };

    const setCategoryId = (newCategoryId) => {
        updateSearchParams((params) => {
            if (newCategoryId) {
                params.set("categoryId", newCategoryId);
            } else {
                params.delete("categoryId");
            }
            // Reset sub-category whenever category changes
            params.delete("subCategoryId");
            params.set("page", "1");
        });
    };

    const setSubCategoryId = (newSubCategoryId) => {
        updateSearchParams((params) => {
            if (newSubCategoryId) {
                params.set("subCategoryId", newSubCategoryId);
            } else {
                params.delete("subCategoryId");
            }
            params.set("page", "1");
        });
    };

    const clearFilters = () => {
        updateSearchParams((params) => {
            params.delete("categoryId");
            params.delete("subCategoryId");
            params.delete("status");
            params.set("page", "1");
        });
    };

    const { data, isLoading, isError, error } = useQuery({
        ...videoQueries.list({ page, limit, status, categoryId, subCategoryId }),
        placeholderData: keepPreviousData,
    });

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
        subCategoryId,
        setSubCategoryId,
        clearFilters,
        videos: data?.videos || [],
        pagination: data?.pagination || { total: 0, page: 1, limit, totalPages: 0 },
        isLoading,
    };
};

export default useVideo;

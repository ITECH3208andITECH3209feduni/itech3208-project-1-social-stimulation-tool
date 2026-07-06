import { userQueries } from "@/api/users/user.queries";
import { toaster } from "@/components/ui/toaster";
import { keepPreviousData, useQuery } from "@tanstack/react-query";
import { useSearchParams } from "react-router-dom";

const useUser = ({ initialLimit = 6 } = {}) => {
    const [searchParams, setSearchParams] = useSearchParams();

    const page = Number(searchParams.get("page")) || 1;
    const limit = Number(searchParams.get("limit")) || initialLimit;
    const role = searchParams.get("role");

    const setPage = (newPage) => {
        setSearchParams((prev) => {
            prev.set("page", String(newPage));
            return prev;
        });
    };

    const setLimit = (newLimit) => {
        setSearchParams((prev) => {
            prev.set("limit", String(newLimit));
            prev.set("page", "1"); // reset page when limit change
            return prev;
        });
    };

    const setRole = (newRole) => {
        setSearchParams((prev) => {
            prev.set("role", newRole);
            prev.set("page", "1"); // reset page when role change
            return prev;
        });
    };

    const { data, error, isLoading, isError } = useQuery({
        ...userQueries.list({ page, limit, role }),
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
        role,
        setRole,
        users: data?.users || [],
        pagination: data?.pagination || { total: 0, page: 1, limit, totalPages: 0 },
        isLoading,
    };
};

export default useUser;

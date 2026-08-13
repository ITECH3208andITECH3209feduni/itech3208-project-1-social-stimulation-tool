import { contactQueries } from "@/api/contacts/contact.queries";
import { toaster } from "@/components/ui/toaster";
import { keepPreviousData, useQuery } from "@tanstack/react-query";
import { useSearchParams } from "react-router-dom";

const useContact = ({ initialLimit = 12 } = {}) => {
    const [searchParams, setSearchParams] = useSearchParams();

    const page = Number(searchParams.get("page")) || 1;
    const limit = Number(searchParams.get("limit")) || initialLimit;

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

    const { data, isLoading, isError, error } = useQuery({
        ...contactQueries.list({ page, limit }),
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
        contacts: data?.contacts || [],
        pagination: data?.pagination || { total: 0, page: 1, limit, totalPages: 0 },
        isLoading,
    };
};

export default useContact;

import { subCategoryApi } from "@/api";
import { useEffect, useState } from "react";

/**
 * Hook to fetch sub-categories for a given categoryId.
 * When categoryId is empty/null, resets the list.
 */
const useSubCategories = (categoryId) => {
    const [subCategories, setSubCategories] = useState([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    useEffect(() => {
        if (!categoryId) {
            setSubCategories([]);
            return;
        }

        const fetchSubCategories = async () => {
            try {
                setLoading(true);
                setError(null);
                const res = await subCategoryApi.getSubCategories({ categoryId });
                if (res.success) {
                    setSubCategories(res.data.subCategories ?? []);
                }
            } catch (err) {
                setError(err);
                setSubCategories([]);
            } finally {
                setLoading(false);
            }
        };

        fetchSubCategories();
    }, [categoryId]);

    return { subCategories, loading, error };
};

export default useSubCategories;

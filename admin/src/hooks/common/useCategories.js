import { categoryApi } from "@/api";
import { useEffect, useState } from "react";

const useCategories = () => {
    const [categories, setCategories] = useState([]);
    const [loading, setLoading] = useState(false);
    const [msg, setMsg] = useState();

    const fetchCategories = async () => {
        try {
            setLoading(true);
            const res = await categoryApi.getCategories();
            if (res.success) {
                setLoading(false);
                setCategories(res.data.categories);
            }
        } catch (error) {
            setLoading(false);
            setMsg(error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchCategories();
    }, []);

    return { categories, loading, msg };
};

export default useCategories;

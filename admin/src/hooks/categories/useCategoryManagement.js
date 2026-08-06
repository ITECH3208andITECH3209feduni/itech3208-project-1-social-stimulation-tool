import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { categoryApi, subCategoryApi } from "@/api";
import { toaster } from "@/components/ui/toaster";

export const categoryQueryKeys = {
    all: ["categories"],
};

export const useCategoryManagement = () => {
    const queryClient = useQueryClient();

    // 1. Fetch categories (with nested subcategories)
    const {
        data,
        isLoading,
        isError,
        error,
        refetch,
    } = useQuery({
        queryKey: categoryQueryKeys.all,
        queryFn: async () => {
            const res = await categoryApi.getCategories();
            if (!res.success) {
                throw new Error(res.message || "Failed to load categories");
            }
            return res.data;
        },
    });

    const categories = data?.categories || [];

    // Helper to invalidate cache
    const invalidateCategories = () => {
        queryClient.invalidateQueries({ queryKey: categoryQueryKeys.all });
    };

    // 2. Create Category Mutation
    const createCategoryMutation = useMutation({
        mutationFn: async (payload) => {
            const res = await categoryApi.createCategory(payload);
            if (!res.success) throw new Error(res.message || "Failed to create category");
            return res;
        },
        onSuccess: (res) => {
            toaster.create({
                title: "Success",
                description: res.message || "Category created successfully",
                type: "success",
            });
            invalidateCategories();
        },
        onError: (err) => {
            toaster.create({
                title: "Error",
                description: err.message || "Failed to create category",
                type: "error",
            });
        },
    });

    // 2b. Update Category Mutation
    const updateCategoryMutation = useMutation({
        mutationFn: async ({ id, payload }) => {
            const res = await categoryApi.updateCategory(id, payload);
            if (!res.success) throw new Error(res.message || "Failed to update category");
            return res;
        },
        onSuccess: (res) => {
            toaster.create({
                title: "Success",
                description: res.message || "Category updated successfully",
                type: "success",
            });
            invalidateCategories();
        },
        onError: (err) => {
            toaster.create({
                title: "Error",
                description: err.message || "Failed to update category",
                type: "error",
            });
        },
    });

    // 3. Delete Category Mutation
    const deleteCategoryMutation = useMutation({
        mutationFn: async (id) => {
            const res = await categoryApi.deleteCategory(id);
            if (!res.success) throw new Error(res.message || "Failed to delete category");
            return res;
        },
        onSuccess: (res) => {
            toaster.create({
                title: "Success",
                description: res.message || "Category deleted successfully",
                type: "success",
            });
            invalidateCategories();
        },
        onError: (err) => {
            toaster.create({
                title: "Error",
                description: err.message || "Failed to delete category",
                type: "error",
            });
        },
    });

    // 4. Create SubCategory Mutation
    const createSubCategoryMutation = useMutation({
        mutationFn: async (payload) => {
            const res = await subCategoryApi.createSubCategory(payload);
            if (!res.success) throw new Error(res.message || "Failed to create sub-category");
            return res;
        },
        onSuccess: (res) => {
            toaster.create({
                title: "Success",
                description: res.message || "Sub-category created successfully",
                type: "success",
            });
            invalidateCategories();
        },
        onError: (err) => {
            toaster.create({
                title: "Error",
                description: err.message || "Failed to create sub-category",
                type: "error",
            });
        },
    });

    // 5. Update SubCategory Mutation
    const updateSubCategoryMutation = useMutation({
        mutationFn: async ({ id, payload }) => {
            const res = await subCategoryApi.updateSubCategory(id, payload);
            if (!res.success) throw new Error(res.message || "Failed to update sub-category");
            return res;
        },
        onSuccess: (res) => {
            toaster.create({
                title: "Success",
                description: res.message || "Sub-category updated successfully",
                type: "success",
            });
            invalidateCategories();
        },
        onError: (err) => {
            toaster.create({
                title: "Error",
                description: err.message || "Failed to update sub-category",
                type: "error",
            });
        },
    });

    // 6. Delete SubCategory Mutation
    const deleteSubCategoryMutation = useMutation({
        mutationFn: async (id) => {
            const res = await subCategoryApi.deleteSubCategory(id);
            if (!res.success) throw new Error(res.message || "Failed to delete sub-category");
            return res;
        },
        onSuccess: (res) => {
            toaster.create({
                title: "Success",
                description: res.message || "Sub-category deleted successfully",
                type: "success",
            });
            invalidateCategories();
        },
        onError: (err) => {
            toaster.create({
                title: "Error",
                description: err.message || "Failed to delete sub-category",
                type: "error",
            });
        },
    });

    return {
        categories,
        isLoading,
        isError,
        error,
        refetch,

        createCategory: createCategoryMutation.mutateAsync,
        isCreatingCategory: createCategoryMutation.isPending,

        updateCategory: updateCategoryMutation.mutateAsync,
        isUpdatingCategory: updateCategoryMutation.isPending,

        deleteCategory: deleteCategoryMutation.mutateAsync,
        isDeletingCategory: deleteCategoryMutation.isPending,

        createSubCategory: createSubCategoryMutation.mutateAsync,
        isCreatingSubCategory: createSubCategoryMutation.isPending,

        updateSubCategory: updateSubCategoryMutation.mutateAsync,
        isUpdatingSubCategory: updateSubCategoryMutation.isPending,

        deleteSubCategory: deleteSubCategoryMutation.mutateAsync,
        isDeletingSubCategory: deleteSubCategoryMutation.isPending,
    };
};

export default useCategoryManagement;

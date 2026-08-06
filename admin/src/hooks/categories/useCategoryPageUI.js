import { useState } from "react";

export const useCategoryPageUI = ({
    createCategory,
    updateCategory,
    deleteCategory,
    createSubCategory,
    updateSubCategory,
    deleteSubCategory,
}) => {
    // Modal States
    const [isCategoryModalOpen, setIsCategoryModalOpen] = useState(false);
    const [categoryEditData, setCategoryEditData] = useState(null);

    const [isSubCategoryModalOpen, setIsSubCategoryModalOpen] = useState(false);
    const [subCategoryEditData, setSubCategoryEditData] = useState(null);

    // Delete Confirmation State
    const [deleteTarget, setDeleteTarget] = useState(null); // { type: 'category' | 'subcategory', data: obj }

    // --- Category Actions ---
    const handleOpenCreateCategory = () => {
        setCategoryEditData(null);
        setIsCategoryModalOpen(true);
    };

    const handleOpenEditCategory = (category) => {
        setCategoryEditData(category);
        setIsCategoryModalOpen(true);
    };

    const handleCloseCategoryModal = () => {
        setIsCategoryModalOpen(false);
        setCategoryEditData(null);
    };

    const handleCategorySubmit = async (payload) => {
        if (categoryEditData && categoryEditData.id) {
            await updateCategory({ id: categoryEditData.id, payload });
        } else {
            await createCategory(payload);
        }
        handleCloseCategoryModal();
    };

    // --- SubCategory Actions ---
    const handleOpenCreateSubCategory = (parentCategory = null) => {
        if (parentCategory) {
            setSubCategoryEditData({ categoryId: parentCategory.id });
        } else {
            setSubCategoryEditData(null);
        }
        setIsSubCategoryModalOpen(true);
    };

    const handleOpenEditSubCategory = (subCat) => {
        setSubCategoryEditData(subCat);
        setIsSubCategoryModalOpen(true);
    };

    const handleCloseSubCategoryModal = () => {
        setIsSubCategoryModalOpen(false);
        setSubCategoryEditData(null);
    };

    const handleSubCategorySubmit = async (payload) => {
        if (subCategoryEditData && subCategoryEditData.id) {
            await updateSubCategory({ id: subCategoryEditData.id, payload });
        } else {
            await createSubCategory(payload);
        }
        handleCloseSubCategoryModal();
    };

    // --- Delete Actions ---
    const handleConfirmDelete = async () => {
        if (!deleteTarget) return;

        if (deleteTarget.type === "category") {
            await deleteCategory(deleteTarget.data.id);
        } else if (deleteTarget.type === "subcategory") {
            await deleteSubCategory(deleteTarget.data.id);
        }

        setDeleteTarget(null);
    };

    return {
        // Category Modal State & Handlers
        isCategoryModalOpen,
        categoryEditData,
        handleOpenCreateCategory,
        handleOpenEditCategory,
        handleCloseCategoryModal,
        handleCategorySubmit,

        // SubCategory Modal State & Handlers
        isSubCategoryModalOpen,
        subCategoryEditData,
        handleOpenCreateSubCategory,
        handleOpenEditSubCategory,
        handleCloseSubCategoryModal,
        handleSubCategorySubmit,

        // Delete State & Handlers
        deleteTarget,
        setDeleteTarget,
        handleConfirmDelete,
    };
};

export default useCategoryPageUI;

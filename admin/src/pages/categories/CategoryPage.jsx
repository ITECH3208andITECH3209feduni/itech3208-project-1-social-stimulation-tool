import {
    Button,
    Heading,
    HStack,
    VStack,
    Text,
    SimpleGrid,
    Spinner,
    Center,
    Flex,
} from "@chakra-ui/react";
import { FaPlus, FaFolderOpen } from "react-icons/fa";
import useCategoryManagement from "@/hooks/categories/useCategoryManagement";
import useCategoryPageUI from "@/hooks/categories/useCategoryPageUI";
import CategoryCard from "@/components/common/cards/CategoryCard";
import CategoryModal from "@/components/common/modals/CategoryModal";
import SubCategoryModal from "@/components/common/modals/SubCategoryModal";
import ConfirmDeleteModal from "@/components/common/modals/ConfirmDeleteModal";

function CategoryPage() {
    const {
        categories,
        isLoading,
        createCategory,
        isCreatingCategory,
        updateCategory,
        isUpdatingCategory,
        deleteCategory,
        isDeletingCategory,
        createSubCategory,
        isCreatingSubCategory,
        updateSubCategory,
        isUpdatingSubCategory,
        deleteSubCategory,
        isDeletingSubCategory,
    } = useCategoryManagement();

    const {
        isCategoryModalOpen,
        categoryEditData,
        handleOpenCreateCategory,
        handleOpenEditCategory,
        handleCloseCategoryModal,
        handleCategorySubmit,

        isSubCategoryModalOpen,
        subCategoryEditData,
        handleOpenCreateSubCategory,
        handleOpenEditSubCategory,
        handleCloseSubCategoryModal,
        handleSubCategorySubmit,

        deleteTarget,
        setDeleteTarget,
        handleConfirmDelete,
    } = useCategoryPageUI({
        createCategory,
        updateCategory,
        deleteCategory,
        createSubCategory,
        updateSubCategory,
        deleteSubCategory,
    });

    return (
        <VStack gap={6} align="stretch">
            {/* Header Section */}
            <Flex justify="space-between" align="center" wrap="wrap" gap={4}>
                <VStack align="start" gap={1}>
                    <Heading color="dark.500" fontSize="30px" fontWeight="bold">
                        Category & Sub-Category Management
                    </Heading>
                    <Text color="gray.400" fontSize="sm">
                        Manage main categories and their nested sub-categories for content classification.
                    </Text>
                </VStack>

                <HStack gap={3}>
                    <Button
                        size="sm"
                        fontSize="xs"
                        px={3}
                        bg="brand.500"
                        _hover={{ bg: "brand.600" }}
                        color="white"
                        onClick={handleOpenCreateCategory}
                    >
                        <FaPlus /> Add Category
                    </Button>
                    <Button
                        size="sm"
                        fontSize="xs"
                        px={3}
                        bg="skyblue.300"
                        _hover={{ bg: "skyblue.400" }}
                        color="white"
                        onClick={() => handleOpenCreateSubCategory(null)}
                    >
                        <FaPlus /> Add Sub-Category
                    </Button>
                </HStack>
            </Flex>

            {/* Main Content Area */}
            {isLoading ? (
                <Center py={16}>
                    <Spinner size="xl" color="brand.500" />
                </Center>
            ) : categories.length === 0 ? (
                <Center py={16} bg="dark.800" rounded="xl" borderWidth="1px" borderColor="whiteAlpha.100">
                    <VStack gap={3}>
                        <FaFolderOpen size={48} color="#718096" />
                        <Text color="gray.400" fontSize="lg">
                            No categories found. Create your first category to get started!
                        </Text>
                        <Button bg="brand.500" color="white" onClick={handleOpenCreateCategory}>
                            <FaPlus /> Create Category
                        </Button>
                    </VStack>
                </Center>
            ) : (
                <SimpleGrid columns={{ base: 1, md: 2, lg: 3 }} gap={6}>
                    {categories.map((category) => (
                        <CategoryCard
                            key={category.id}
                            category={category}
                            onAddSubCategory={handleOpenCreateSubCategory}
                            onEditCategory={handleOpenEditCategory}
                            onDeleteCategory={(cat) => setDeleteTarget({ type: "category", data: cat })}
                            onEditSubCategory={handleOpenEditSubCategory}
                            onDeleteSubCategory={(subCat) => setDeleteTarget({ type: "subcategory", data: subCat })}
                        />
                    ))}
                </SimpleGrid>
            )}

            {/* Modals & Dialogs */}
            <CategoryModal
                isOpen={isCategoryModalOpen}
                onClose={handleCloseCategoryModal}
                onSubmit={handleCategorySubmit}
                isLoading={isCreatingCategory || isUpdatingCategory}
                initialData={categoryEditData}
            />

            <SubCategoryModal
                isOpen={isSubCategoryModalOpen}
                onClose={handleCloseSubCategoryModal}
                onSubmit={handleSubCategorySubmit}
                isLoading={isCreatingSubCategory || isUpdatingSubCategory}
                categories={categories}
                initialData={subCategoryEditData}
            />

            <ConfirmDeleteModal
                isOpen={Boolean(deleteTarget)}
                onClose={() => setDeleteTarget(null)}
                onConfirm={handleConfirmDelete}
                isLoading={isDeletingCategory || isDeletingSubCategory}
                title={
                    deleteTarget?.type === "category"
                        ? `Delete Category "${deleteTarget?.data?.name}"`
                        : `Delete Sub-Category "${deleteTarget?.data?.name}"`
                }
                message={
                    deleteTarget?.type === "category"
                        ? `Are you sure you want to delete the category "${deleteTarget?.data?.name}"? All related data may be affected.`
                        : `Are you sure you want to delete the sub-category "${deleteTarget?.data?.name}"?`
                }
            />
        </VStack>
    );
}

export default CategoryPage;

import { useState, useEffect } from "react";
import { Dialog, Portal, VStack, HStack, Button, CloseButton } from "@chakra-ui/react";
import NormalField from "@/components/common/fields/NormalField";
import TextareaField from "@/components/common/fields/TextareaField";
import SelectionField from "@/components/common/fields/SelectionField";

function SubCategoryModal({ isOpen, onClose, onSubmit, isLoading, categories = [], initialData = null }) {
    const [categoryId, setCategoryId] = useState("");
    const [name, setName] = useState("");
    const [description, setDescription] = useState("");

    const isEdit = Boolean(initialData && initialData.id);

    useEffect(() => {
        if (isOpen) {
            if (initialData) {
                const selectedCatId = initialData.categoryId?.id || initialData.categoryId || initialData.category?.id || "";
                setCategoryId(selectedCatId);
                setName(initialData.name || "");
                setDescription(initialData.description || "");
            } else {
                setCategoryId("");
                setName("");
                setDescription("");
            }
        }
    }, [isOpen, initialData]);

    const handleSubmit = (e) => {
        e.preventDefault();
        if (!name.trim() || !categoryId) return;
        onSubmit({
            name: name.trim(),
            categoryId,
            description: description.trim(),
        });
    };

    const categoryItems = categories.map((cat) => ({
        id: cat.id,
        name: cat.name,
    }));

    return (
        <Dialog.Root
            open={isOpen}
            placement="center"
            onOpenChange={(e) => {
                if (!e.open) onClose();
            }}
        >
            <Portal>
                <Dialog.Backdrop />
                <Dialog.Positioner>
                    <Dialog.Content bg="dark.900" color="whiteAlpha.900" maxW="550px">
                        <Dialog.Header borderBottomWidth="1px" borderColor="whiteAlpha.200">
                            <Dialog.Title fontSize="xl" fontWeight="bold">
                                {isEdit ? "Edit Sub-Category" : "Create New Sub-Category"}
                            </Dialog.Title>
                        </Dialog.Header>
                        <form onSubmit={handleSubmit}>
                            <Dialog.Body py={6}>
                                <VStack gap={4} align="stretch">
                                    <SelectionField
                                        fieldLabel="Parent Category"
                                        inputPlaceholder="Select parent category"
                                        items={categoryItems}
                                        value={categoryId}
                                        onChange={(e) => setCategoryId(e.target.value)}
                                        required
                                    />

                                    <NormalField
                                        fieldLabel="Sub-Category Name"
                                        inputPlaceholder="Enter sub-category name (e.g., Dentist Visit, Job Interview)"
                                        name="name"
                                        value={name}
                                        onChange={(e) => setName(e.target.value)}
                                        required
                                    />

                                    <TextareaField
                                        fieldLabel="Description"
                                        placeholder="Optional sub-category description..."
                                        name="description"
                                        value={description}
                                        onChange={(e) => setDescription(e.target.value)}
                                        maxLength={200}
                                        rows={3}
                                    />
                                </VStack>
                            </Dialog.Body>
                            <Dialog.Footer borderTopWidth="1px" borderColor="whiteAlpha.100">
                                <HStack justify="flex-end" gap={3} w="full">
                                    <Button
                                        size="sm"
                                        fontSize="xs"
                                        px={4}
                                        variant="outline"
                                        borderColor="whiteAlpha.300"
                                        color="whiteAlpha.800"
                                        _hover={{ bg: "whiteAlpha.100", color: "white" }}
                                        onClick={onClose}
                                        disabled={isLoading}
                                    >
                                        Cancel
                                    </Button>
                                    <Button
                                        type="submit"
                                        size="sm"
                                        fontSize="xs"
                                        px={4}
                                        bg="brand.500"
                                        _hover={{ bg: "brand.600" }}
                                        color="white"
                                        loading={isLoading}
                                        disabled={!name.trim() || !categoryId}
                                    >
                                        {isEdit ? "Save Changes" : "Create Sub-Category"}
                                    </Button>
                                </HStack>
                            </Dialog.Footer>
                        </form>
                        <Dialog.CloseTrigger asChild>
                            <CloseButton size="sm" />
                        </Dialog.CloseTrigger>
                    </Dialog.Content>
                </Dialog.Positioner>
            </Portal>
        </Dialog.Root>
    );
}

export default SubCategoryModal;

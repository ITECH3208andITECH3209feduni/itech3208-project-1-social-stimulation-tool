import { useState, useEffect } from "react";
import { Dialog, Portal, VStack, HStack, Button, CloseButton } from "@chakra-ui/react";
import NormalField from "@/components/common/fields/NormalField";

function CategoryModal({ isOpen, onClose, onSubmit, isLoading, initialData = null }) {
    const [name, setName] = useState("");

    const isEdit = Boolean(initialData && initialData.id);

    useEffect(() => {
        if (isOpen) {
            setName(initialData?.name || "");
        } else {
            setName("");
        }
    }, [isOpen, initialData]);

    const handleSubmit = (e) => {
        e.preventDefault();
        if (!name.trim()) return;
        onSubmit({ name: name.trim() });
    };

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
                    <Dialog.Content bg="dark.900" color="whiteAlpha.900" maxW="500px">
                        <Dialog.Header borderBottomWidth="1px" borderColor="whiteAlpha.200">
                            <Dialog.Title fontSize="xl" fontWeight="bold">
                                {isEdit ? "Edit Category" : "Create New Category"}
                            </Dialog.Title>
                        </Dialog.Header>
                        <form onSubmit={handleSubmit}>
                            <Dialog.Body py={6}>
                                <VStack gap={4} align="stretch">
                                    <NormalField
                                        fieldLabel="Category Name"
                                        inputPlaceholder="Enter category name (e.g., Medical, Social)"
                                        name="name"
                                        value={name}
                                        onChange={(e) => setName(e.target.value)}
                                        required
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
                                        disabled={!name.trim()}
                                    >
                                        {isEdit ? "Save Changes" : "Create Category"}
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

export default CategoryModal;

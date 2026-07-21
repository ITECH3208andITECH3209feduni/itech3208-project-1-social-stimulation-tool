import { Button, CloseButton, Dialog, HStack, Portal, Text } from "@chakra-ui/react";

function VideoDeleteDialog({ isOpen, onClose, onSave, isDeleting, video }) {
    return (
        <Dialog.Root
            open={isOpen}
            placement={"center"}
            onOpenChange={(e) => {
                if (!e.open) onClose();
            }}
        >
            <Portal>
                <Dialog.Backdrop />
                <Dialog.Positioner>
                    <Dialog.Content bg={"dark.900"}>
                        <Dialog.Header>
                            <Dialog.Title>Delete Video</Dialog.Title>
                        </Dialog.Header>
                        <Dialog.Body>
                            <Text color={"brand.200"}>
                                Are you sure you want to delete the video "{video?.title}"? 
                                This action cannot be undone.
                            </Text>
                        </Dialog.Body>
                        <Dialog.Footer>
                            <HStack w={200}>
                                <Button flex={1} bg={"brand.300"} onClick={() => onClose()} disabled={isDeleting}>
                                    Cancel
                                </Button>
                                <Button flex={1} bg={"skyblue.300"} onClick={onSave} loading={isDeleting}>
                                    Delete
                                </Button>
                            </HStack>
                        </Dialog.Footer>
                        <Dialog.CloseTrigger asChild>
                            <CloseButton size="sm" />
                        </Dialog.CloseTrigger>
                    </Dialog.Content>
                </Dialog.Positioner>
            </Portal>
        </Dialog.Root>
    );
}

export default VideoDeleteDialog;

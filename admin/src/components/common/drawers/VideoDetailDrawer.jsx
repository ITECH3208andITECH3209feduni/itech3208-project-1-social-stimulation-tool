import { Button, CloseButton, Drawer, Portal, HStack, VStack, Text, Box, Flex, Image } from "@chakra-ui/react";
import NormalField from "../fields/NormalField";
import TextareaField from "../fields/TextareaField";
import FileUploadField from "../fields/FileUploadField";
import useVideoDetailDrawerForm from "@/hooks/videos/useVideoDetailDrawerForm";
import { formatDuration } from "@/utils/formatDuration";

function VideoDetailDrawer({ isOpen, onClose, onSave, video }) {
    const {
        formData,
        handleInputChange,
        handleSave,
    } = useVideoDetailDrawerForm(video, isOpen, onSave);

    const thumbnailUrl =
        typeof video?.thumbnail === "object" ? video?.thumbnail?.url : video?.thumbnail;

    return (
        <Drawer.Root
            size={"md"}
            open={isOpen}
            onOpenChange={(e) => {
                if (!e.open) onClose();
            }}
        >
            <Portal>
                <Drawer.Backdrop />
                <Drawer.Positioner>
                    <Drawer.Content bg={"dark.900"} color="whiteAlpha.900">
                        <Drawer.Header borderBottomWidth="1px" borderColor="whiteAlpha.200">
                            <Drawer.Title>Update Video Details</Drawer.Title>
                        </Drawer.Header>
                        <Drawer.Body py={6}>
                            <VStack gap={6} align="stretch">
                                {/* Current Thumbnail Preview */}
                                {video && (
                                    <Box position={"relative"} w="full" rounded="md" overflow="hidden">
                                        <Image
                                            w={"100%"}
                                            h={"auto"}
                                            aspectRatio={16 / 9}
                                            src={thumbnailUrl}
                                            objectFit={"cover"}
                                            alt="Current Thumbnail"
                                        />
                                        <Text
                                            position="absolute"
                                            bottom={2}
                                            right={2}
                                            bg="blackAlpha.800"
                                            color="white"
                                            px={2}
                                            py={0.5}
                                            rounded="md"
                                            fontSize="sm"
                                            fontWeight="medium"
                                        >
                                            {formatDuration(video.duration)}
                                        </Text>
                                    </Box>
                                )}

                                <FileUploadField
                                    name="video"
                                    fieldLabel="Replace Video File (Optional)"
                                    accept={["video/mp4", "video/webm", "video/ogg"]}
                                    helpText="Upload a new video to replace the existing one. Thumbnail and duration will be updated automatically."
                                    value={formData.video}
                                    onFileChange={(file) => handleInputChange("video", file)}
                                    onFileRemove={() => handleInputChange("video", null)}
                                />

                                <NormalField
                                    fieldLabel="Title"
                                    inputPlaceholder="Enter video title"
                                    name="title"
                                    value={formData.title}
                                    onChange={(e) => handleInputChange("title", e.target.value)}
                                />

                                <TextareaField
                                    name="description"
                                    fieldLabel="Description"
                                    placeholder="Enter video description..."
                                    required={false}
                                    maxLength={2000}
                                    rows={4}
                                    autoresize={true}
                                    value={formData.description}
                                    onChange={(e) => handleInputChange("description", e.target.value)}
                                />
                            </VStack>
                        </Drawer.Body>
                        <Drawer.Footer borderTopWidth="1px" borderColor="whiteAlpha.200">
                            <HStack w="full" justify="flex-end" gap={3}>
                                <Button variant="ghost" onClick={() => onClose()}>
                                    Cancel
                                </Button>
                                <Button bg={"brand.500"} onClick={handleSave}>
                                    Save Changes
                                </Button>
                            </HStack>
                        </Drawer.Footer>
                        <Drawer.CloseTrigger asChild>
                            <CloseButton size="sm" />
                        </Drawer.CloseTrigger>
                    </Drawer.Content>
                </Drawer.Positioner>
            </Portal>
        </Drawer.Root>
    );
}

export default VideoDetailDrawer;

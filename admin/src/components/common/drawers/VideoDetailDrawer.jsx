import { Button, CloseButton, Drawer, Portal, HStack, VStack, Text, Box, Image } from "@chakra-ui/react";
import NormalField from "../fields/NormalField";
import TextareaField from "../fields/TextareaField";
import FileUploadField from "../fields/FileUploadField";
import SelectionField from "../fields/SelectionField";
import useVideoDetailDrawerForm from "@/hooks/videos/useVideoDetailDrawerForm";
import useCategories from "@/hooks/common/useCategories";
import useSubCategories from "@/hooks/common/useSubCategories";
import { formatDuration } from "@/utils/formatDuration";

function VideoDetailDrawer({ isOpen, onClose, onSave, video }) {
    const {
        formData,
        handleInputChange,
        handleCategoryChange,
        handleSave,
    } = useVideoDetailDrawerForm(video, isOpen, onSave);

    const { categories } = useCategories();
    const { subCategories } = useSubCategories(formData.categoryId);

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

                                <SelectionField
                                    items={categories}
                                    fieldLabel="Category"
                                    inputPlaceholder="Choose category"
                                    value={formData.categoryId}
                                    onChange={handleCategoryChange}
                                />

                                {/* Sub-category selector — only visible once a category is selected */}
                                {formData.categoryId && (
                                    <SelectionField
                                        items={subCategories}
                                        fieldLabel="Sub-Category"
                                        inputPlaceholder={
                                            subCategories.length === 0
                                                ? "No sub-categories available"
                                                : "Choose sub-category"
                                        }
                                        value={formData.subCategoryId}
                                        onChange={(e) =>
                                            handleInputChange("subCategoryId", e.target.value)
                                        }
                                    />
                                )}
                            </VStack>
                        </Drawer.Body>
                        <Drawer.Footer borderTopWidth="1px" borderColor="whiteAlpha.200" px={5} py={4}>
                            <HStack w="full" justify="flex-end" gap={3}>
                                <Button
                                    bg="whiteAlpha.100"
                                    color="whiteAlpha.900"
                                    px={6}
                                    style={{ border: "1px solid rgba(255,255,255,0.25)" }}
                                    _hover={{ bg: "whiteAlpha.200", color: "white" }}
                                    _active={{ bg: "whiteAlpha.300" }}
                                    transition="all 0.15s ease"
                                    onClick={() => onClose()}
                                >
                                    Cancel
                                </Button>
                                <Button
                                    bg="brand.500"
                                    color="white"
                                    px={6}
                                    fontWeight="semibold"
                                    _hover={{ bg: "brand.600", transform: "translateY(-1px)", boxShadow: "0 4px 12px rgba(0,0,0,0.3)" }}
                                    _active={{ transform: "translateY(0)", boxShadow: "none" }}
                                    transition="all 0.15s ease"
                                    onClick={handleSave}
                                >
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

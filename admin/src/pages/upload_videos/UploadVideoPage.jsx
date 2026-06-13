import { Box, Button, VStack, Heading } from "@chakra-ui/react";
import { toaster } from "@/components/ui/toaster";
import { useState } from "react";
import NormalField from "@/components/common/fields/NormalField";
import TagsInputField from "@/components/common/fields/TagsInputField";
import FileUploadField from "@/components/common/fields/FileUploadField";
import TextareaField from "@/components/common/fields/TextareaField";
import SelectionField from "@/components/common/fields/SelectionField";
import useCategories from "@/hooks/common/useCategories";
import useCreateVideo from "@/hooks/common/useCreateVideo";
import { buildVideoFormData } from "@/utils/buildVideoFormData";

function UploadVideoPage() {
    const { categories } = useCategories();
    const { loading, createVideo } = useCreateVideo();

    const [formData, setFormData] = useState({
        video: null,
        title: "",
        description: "",
        categoryId: "",
        tags: [],
    });

    const handleInputChange = (key, value) => setFormData((prev) => ({ ...prev, [key]: value }));

    const handleUpload = async () => {
        const payload = buildVideoFormData(formData);
        await createVideo(payload, {
            onSuccess: (_, msg) => {
                toaster.create({
                    description: msg,
                    type: "success",
                });
                clearForm();
            },
            onError: (msg) => {
                toaster.create({
                    description: msg,
                    type: "error",
                });
            },
        });
    };

    const clearForm = () =>
        setFormData({
            video: null,
            title: "",
            description: "",
            categoryId: "",
            tags: [],
        });

    return (
        <VStack spacing={8} align="stretch" position={"relative"}>
            {/* Publish Form Section */}
            <Heading alignSelf={"start"} color="dark.500" fontSize={"30px"} fontWeight="bold">
                Video Upload
            </Heading>
            <Box display="flex" alignItems="center" justifyContent="center">
                <Box maxW="100%" w="full" p={6} bg="dark" borderRadius="md" boxShadow="md">
                    <Box>
                        <VStack gap={"4"} align="stretch">
                            <FileUploadField
                                name="video"
                                fieldLabel="Video File"
                                accept={["video/mp4", "video/webm", "video/ogg"]}
                                helpText="Click or drag a video file here (MP4, WebM)"
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
                                placeholder="Tell us more about your needs and how we can help you..."
                                required={true}
                                maxLength={500}
                                rows={3}
                                autoresize={true}
                                helpText="Max 500 characters."
                                value={formData.description}
                                onChange={(e) => handleInputChange("description", e.target.value)}
                            />

                            <SelectionField
                                items={categories}
                                fieldLabel={"Category"}
                                inputPlaceholder="Choose category"
                                value={formData.categoryId}
                                onChange={(e) => handleInputChange("categoryId", e.target.value)}
                            />

                            <TagsInputField
                                name="tags"
                                value={formData.tags}
                                onChange={(tags) => handleInputChange("tags", tags)}
                            />

                            <Button
                                type="button"
                                bg="brand.500"
                                p={"4"}
                                onClick={handleUpload}
                                loading={loading}
                                loadingText="Uploading..."
                            >
                                Upload Video
                            </Button>
                        </VStack>
                    </Box>
                </Box>
            </Box>
        </VStack>
    );
}

export default UploadVideoPage;

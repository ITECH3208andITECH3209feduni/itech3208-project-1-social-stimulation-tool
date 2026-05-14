import PublishMediaCard from "@/components/cards/PublishMediaCard";
import { Box, Button, Input, Textarea, VStack, Field, Grid, Heading } from "@chakra-ui/react";
import { useState, useEffect } from "react";
import EmptyState from "@/components/common/EmptyState";
import { BiVideo } from "react-icons/bi";

const getYouTubeEmbedUrl = (url) => {
    try {
        const parsed = new URL(url);
        let videoId = null;

        if (parsed.hostname.includes("youtube.com")) {
            videoId = parsed.searchParams.get("v");
        } else if (parsed.hostname.includes("youtu.be")) {
            videoId = parsed.pathname.slice(1);
        }

        if (!videoId) return null;
        return `https://www.youtube.com/embed/${videoId}`;
    } catch {
        return null;
    }
};

function UploadVideoPage() {
    const [form, setForm] = useState({
        title: "",
        description: "",
        url: "",
    });

    const [errors, setErrors] = useState({
        title: "",
        description: "",
        url: "",
    });

    const [videos, setVideos] = useState(() => {
        try {
            const stored = localStorage.getItem("publishedVideos");
            return stored ? JSON.parse(stored) : [];
        } catch {
            return [];
        }
    });
    // Save to localStorage whenever videos change
    useEffect(() => {
        localStorage.setItem("publishedVideos", JSON.stringify(videos));
    }, [videos]);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setForm((prev) => ({ ...prev, [name]: value }));
        setErrors((prev) => ({ ...prev, [name]: "" }));
    };

    const validate = () => {
        const newErrors = { title: "", description: "", url: "" };
        let valid = true;

        if (!form.title.trim()) {
            newErrors.title = "Title is required.";
            valid = false;
        }
        if (!form.description.trim()) {
            newErrors.description = "Description is required.";
            valid = false;
        }
        if (!form.url.trim()) {
            newErrors.url = "URL is required.";
            valid = false;
        } else if (!getYouTubeEmbedUrl(form.url)) {
            newErrors.url = "Please enter a valid YouTube URL.";
            valid = false;
        }

        setErrors(newErrors);
        return valid;
    };
    const handleSubmit = (e) => {
        e.preventDefault();
        if (!validate()) return;

        const embedUrl = getYouTubeEmbedUrl(form.url);

        const newVideo = {
            id: Date.now(),
            title: form.title,
            description: form.description,
            url: embedUrl,
            originalUrl: form.url, // ← keep the original watch URL
            actions: ["edit", "delete", "open"],
        };

        setVideos((prev) => [newVideo, ...prev]);
        setForm({ title: "", description: "", url: "" });
    };

    const handleMediaClick = (media) => {
        if (media?.originalUrl) window.open(media.originalUrl, "_blank");
    };

    const handleEdit = (media) => {
        // TODO: populate form or open edit drawer
    };

    const handleDelete = (media) => {
        setVideos((prev) => prev.filter((v) => v.id !== media.id));
    };

    return (
        <VStack spacing={8} align="stretch">
            {/* Publish Form Section */}
            <Heading alignSelf={"start"} color="dark.500" fontSize={"30px"} fontWeight="bold">
                Video Upload
            </Heading>
            <Box display="flex" alignItems="center" justifyContent="center">
                <Box maxW="100%" w="full" p={6} bg="dark" borderRadius="md" boxShadow="md">
                    <Heading size="lg" color="white" mb={6} textAlign="center">
                        Upload new video
                    </Heading>
                    <form onSubmit={handleSubmit}>
                        <VStack gap={"4"}>
                            <Field.Root invalid={!!errors.title}>
                                <Field.Label color="white">Title</Field.Label>
                                <Input
                                    name="title"
                                    value={form.title}
                                    onChange={handleChange}
                                    color="brand.500"
                                    p={"3"}
                                />
                                <Field.ErrorText>{errors.title}</Field.ErrorText>
                            </Field.Root>

                            <Field.Root invalid={!!errors.description}>
                                <Field.Label color="white">Description</Field.Label>
                                <Textarea
                                    name="description"
                                    value={form.description}
                                    onChange={handleChange}
                                    size="xl"
                                    color="brand.500"
                                    p={"3"}
                                />
                                <Field.ErrorText>{errors.description}</Field.ErrorText>
                            </Field.Root>

                            <Field.Root invalid={!!errors.url}>
                                <Field.Label color="white">YouTube URL</Field.Label>
                                <Input
                                    name="url"
                                    value={form.url}
                                    onChange={handleChange}
                                    placeholder="https://www.youtube.com/watch?v=..."
                                    color="brand.500"
                                    p={"3"}
                                />
                                <Field.ErrorText>{errors.url}</Field.ErrorText>
                            </Field.Root>

                            <Button type="submit" bg="brand.500" p={"4"}>
                                Upload Video
                            </Button>
                        </VStack>
                    </form>
                </Box>
            </Box>

            {/* Published Media Grid Section */}
            <Box>
                <Heading size="lg" color="white" mb={6}>
                    Your Published Media
                </Heading>

                {videos.length > 0 ? (
                    <Grid templateColumns="repeat(auto-fill, minmax(280px, 1fr))" gap={6}>
                        {videos.map((media) => (
                            <PublishMediaCard
                                key={media.id}
                                media={media}
                                onClick={() => handleMediaClick(media)}
                                onEdit={handleEdit}
                                onDelete={handleDelete}
                            />
                        ))}
                    </Grid>
                ) : (
                    <EmptyState
                        title="No published media"
                        description="You haven't published any media yet. Use the form above to publish your first media."
                        icon={BiVideo}
                    />
                )}
            </Box>
        </VStack>
    );
}

export default UploadVideoPage;

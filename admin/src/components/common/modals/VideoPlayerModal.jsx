import { Dialog, Box, VStack, HStack, Text, IconButton, Badge } from "@chakra-ui/react";
import ReactPlayer from "react-player";
import { MdClose } from "react-icons/md";
import { formatDuration } from "@/utils/formatDuration";

function VideoPlayerModal({ video, isOpen, onClose }) {
    if (!video) return null;

    const videoUrl = typeof video.video === "object" ? video.video?.url : video.video;
    const thumbnailUrl =
        typeof video.thumbnail === "object" ? video.thumbnail?.url : video.thumbnail;

    return (
        <Dialog.Root
            open={isOpen}
            onOpenChange={(detail) => !detail.open && onClose()}
            size="cover"
        >
            <Dialog.Backdrop bg="blackAlpha.800" backdropFilter="blur(6px)" />

            <Dialog.Positioner>
                <Dialog.Content
                    bg="dark.900"
                    rounded="xl"
                    overflow="hidden"
                    boxShadow="0 25px 60px rgba(0,0,0,0.8)"
                    maxW={{ base: "95vw", md: "900px" }}
                    w="full"
                    mx="auto"
                >
                    {/* Header */}
                    <HStack
                        px={5}
                        py={4}
                        borderBottomWidth="1px"
                        borderColor="whiteAlpha.200"
                        justify="space-between"
                        align="center"
                    >
                        <VStack align="start" gap={0} flex={1} overflow="hidden">
                            <Text color="white" fontWeight="bold" fontSize="lg" noOfLines={1}>
                                {video.title}
                            </Text>
                            <HStack gap={2} mt={1} flexWrap="wrap">
                                {video.duration && (
                                    <Badge
                                        bg="red.500"
                                        color="white"
                                        fontSize="xs"
                                        px={2}
                                        py={0.5}
                                        borderRadius="md"
                                        fontWeight="semibold"
                                    >
                                        {formatDuration(video.duration)}
                                    </Badge>
                                )}
                                {video.category?.name && (
                                    <Badge
                                        bg="blue.500"
                                        color="white"
                                        fontSize="xs"
                                        px={2}
                                        py={0.5}
                                        borderRadius="md"
                                        fontWeight="semibold"
                                    >
                                        {video.category.name}
                                    </Badge>
                                )}
                                {video.subCategory?.name && (
                                    <Badge
                                        bg="purple.500"
                                        color="white"
                                        fontSize="xs"
                                        px={2}
                                        py={0.5}
                                        borderRadius="md"
                                        fontWeight="semibold"
                                    >
                                        {video.subCategory.name}
                                    </Badge>
                                )}
                            </HStack>
                        </VStack>

                        <IconButton
                            aria-label="Close"
                            variant="ghost"
                            color="whiteAlpha.700"
                            _hover={{ color: "white", bg: "whiteAlpha.200" }}
                            rounded="full"
                            onClick={onClose}
                        >
                            <MdClose size={22} />
                        </IconButton>
                    </HStack>

                    {/* Video Player */}
                    <Box bg="black" w="full" aspectRatio={16 / 9}>
                        <ReactPlayer
                            src={videoUrl}
                            playing={isOpen}
                            controls
                            width="100%"
                            height="100%"
                            style={{ display: "block" }}
                            onError={(e) =>
                                console.error("[VideoPlayerModal] ReactPlayer error:", e)
                            }
                            config={{
                                file: {
                                    attributes: {
                                        controlsList: "nodownload",
                                        disablePictureInPicture: false,
                                    },
                                },
                            }}
                        />
                    </Box>

                    {/* Description */}
                    {video.description && (
                        <Box px={5} py={4} borderTopWidth="1px" borderColor="whiteAlpha.100">
                            <Text color="whiteAlpha.600" fontSize="sm" lineHeight="tall">
                                {video.description}
                            </Text>
                        </Box>
                    )}
                </Dialog.Content>
            </Dialog.Positioner>
        </Dialog.Root>
    );
}

export default VideoPlayerModal;

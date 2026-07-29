import {
    Image,
    Card,
    HStack,
    VStack,
    Button,
    Box,
    Float,
    IconButton,
    Text,
} from "@chakra-ui/react";
import { useState } from "react";
import { FaPlay } from "react-icons/fa";
import { formatDuration } from "@/utils/formatDuration";
import VideoPlayerModal from "@/components/common/modals/VideoPlayerModal";

import useVideoUIStore from "@/hooks/stores/useVideoUIStore";

function VideoCard({ video }) {
    const [isModalOpen, setIsModalOpen] = useState(false);
    const { openUpdateDrawer, openDeleteDialog } = useVideoUIStore();

    const rawThumbnail = typeof video.thumbnail === "object" ? video.thumbnail?.url : video.thumbnail;
    const videoUrl = typeof video.video === "object" ? video.video?.url : video.video;
    const thumbnailUrl = rawThumbnail || (videoUrl ? videoUrl.replace(/\.[^/.]+$/, ".jpg") : "");

    return (
        <>
            <VStack
                bg={"dark.800"}
                rounded={"md"}
                overflow={"hidden"}
                transition="transform 0.2s, box-shadow 0.2s"
                _hover={{ transform: "translateY(-2px)", boxShadow: "0 8px 30px rgba(0,0,0,0.5)" }}
                cursor="pointer"
            >
                {/* Thumbnail + Play overlay */}
                <Box position={"relative"} w="full" onClick={() => setIsModalOpen(true)}>
                    <Image
                        flex={1}
                        w={"100%"}
                        h={"auto"}
                        aspectRatio={16 / 9}
                        src={thumbnailUrl}
                        objectFit={"cover"}
                    />

                    {/* Hover overlay */}
                    <Box
                        position="absolute"
                        inset={0}
                        bg="blackAlpha.400"
                        opacity={0}
                        _hover={{ opacity: 1 }}
                        transition="opacity 0.2s"
                        display="flex"
                        alignItems="center"
                        justifyContent="center"
                    >
                        <IconButton
                            size={"2xl"}
                            color={"whiteAlpha.900"}
                            bg={"brand.300"}
                            _hover={{ bg: "brand.400", transform: "scale(1.1)" }}
                            rounded={"full"}
                            transition="all 0.15s"
                            aria-label="Play video"
                        >
                            <FaPlay />
                        </IconButton>
                    </Box>

                    <Float placement={"bottom-end"} offsetX={8} offsetY={4}>
                        <Text
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
                    </Float>
                </Box>

                <Card.Root flex={1} w={"100%"} bg="transparent" borderWidth={0}>
                    <Card.Body gap="2" color={"whiteAlpha.900"}>
                        <Card.Title fontSize={18} noOfLines={1}>
                            {video.title}
                        </Card.Title>
                        <Card.Description color={"gray.400"} noOfLines={2}>
                            {video.description}
                        </Card.Description>
                    </Card.Body>
                    <Card.Footer>
                        <HStack w={"100%"} justify={"end"}>
                            <Button flex="1" color={"whiteAlpha.900"} bg={"skyblue.300"} onClick={() => openUpdateDrawer(video)}>
                                Edit
                            </Button>
                            <Button flex="1" color={"whiteAlpha.900"} bg={"brand.300"} onClick={() => openDeleteDialog(video)}>
                                Delete
                            </Button>
                        </HStack>
                    </Card.Footer>
                </Card.Root>
            </VStack>

            <VideoPlayerModal
                video={video}
                isOpen={isModalOpen}
                onClose={() => setIsModalOpen(false)}
            />
        </>
    );
}

export default VideoCard;

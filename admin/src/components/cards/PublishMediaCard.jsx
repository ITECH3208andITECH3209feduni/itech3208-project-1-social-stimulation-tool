import React, { useState } from "react";
import { Box, Text, HStack, VStack, IconButton } from "@chakra-ui/react";
import { FaRegTrashAlt } from "react-icons/fa";
import { FiExternalLink } from "react-icons/fi";
import { MdEdit } from "react-icons/md";

function PublishMediaCard({ media, onEdit, onDelete, onClick }) {
    const [videos, setVideos] = useState(() => {
        try {
            const stored = localStorage.getItem("publishedVideos");
            return stored ? JSON.parse(stored) : [];
        } catch {
            return [];
        }
    });
    const handleEdit = (e) => {
        e.stopPropagation();
        onEdit?.(media);
    };

    const handleDelete = (e) => {
        e.stopPropagation();
        onDelete?.(media);
    };

    const handleOpenNewTab = (e) => {
        e.stopPropagation();
        if (media?.originalUrl) window.open(media.originalUrl, "_blank");
    };

    const handleCardClick = () => {
        if (media?.originalUrl) window.open(media.originalUrl, "_blank");
        onClick?.(media);
    };

    return (
        <Box
            bg="dark.800"
            borderRadius="md"
            overflow="hidden"
            boxShadow="md"
            cursor="pointer"
            onClick={handleCardClick}
            _hover={{ transform: "scale(1.02)", transition: "transform 0.2s" }}
        >
            {/* Video Embed / Thumbnail */}
            <Box position="relative" h="160px" bg="dark.700" role="group">
                {media?.url ? (
                    <>
                        <Box
                            as="iframe"
                            src={media.url}
                            w="full"
                            h="full"
                            border="none"
                            pointerEvents="none"
                        />
                        {/* Play overlay on hover */}
                        <Box
                            position="absolute"
                            inset={0}
                            display="flex"
                            alignItems="center"
                            justifyContent="center"
                            bg="blackAlpha.500"
                            opacity={0}
                            _groupHover={{ opacity: 1 }}
                            transition="opacity 0.2s"
                            color="white"
                            fontSize="5xl"
                        >
                            ▶
                        </Box>
                    </>
                ) : (
                    <Box
                        w="full"
                        h="full"
                        display="flex"
                        alignItems="center"
                        justifyContent="center"
                        color="gray.500"
                        fontSize="3xl"
                    >
                        🎬
                    </Box>
                )}
            </Box>

            {/* Card Body */}
            <VStack align="stretch" p={4} spacing={2}>
                <Text color="white" fontWeight="bold" fontSize="md" noOfLines={1}>
                    {media?.title || "Untitled"}
                </Text>

                <Text color="gray.400" fontSize="sm" noOfLines={2}>
                    {media?.description || "No description provided."}
                </Text>

                {/* Actions */}
                <HStack justify="flex-end" mt={2} spacing={2}>
                    {media?.actions?.includes("edit") && (
                        <IconButton
                            size="sm"
                            aria-label="Edit video"
                            color="blue.300"
                            bg="dark.700"
                            onClick={handleEdit}
                            _hover={{ bg: "blue.800", color: "blue.100" }}
                        >
                            <MdEdit />
                        </IconButton>
                    )}
                    {media?.actions?.includes("delete") && (
                        <IconButton
                            size="sm"
                            aria-label="Delete video"
                            color="red.400"
                            bg="dark.700"
                            onClick={handleDelete}
                            _hover={{ bg: "red.800", color: "red.100" }}
                        >
                            <FaRegTrashAlt />
                        </IconButton>
                    )}
                    {media?.actions?.includes("open") && (
                        <IconButton
                            size="sm"
                            aria-label="Open on YouTube"
                            color="green.400"
                            bg="dark.700"
                            onClick={handleOpenNewTab}
                            _hover={{ bg: "green.800", color: "green.100" }}
                        >
                            <FiExternalLink />
                        </IconButton>
                    )}
                </HStack>
            </VStack>
        </Box>
    );
}

export default PublishMediaCard;

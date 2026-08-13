import { Box, Card, HStack, Text, VStack, Spacer, IconButton } from "@chakra-ui/react";
import { FaStar, FaRegStar, FaTrash, FaThumbtack } from "react-icons/fa";
import UserAvatar from "@/components/common/avatar/UserAvatar";

function StarRating({ rating }) {
    return (
        <HStack gap={0.5}>
            {Array.from({ length: 5 }).map((_, i) => (
                <Box key={i} color={i < rating ? "yellow.400" : "whiteAlpha.300"} fontSize="xs">
                    {i < rating ? <FaStar /> : <FaRegStar />}
                </Box>
            ))}
        </HStack>
    );
}

function FeedbackCard({ feedback, onTogglePin, onDelete, isTogglingPin, isDeleting }) {
    const avatarUrl = feedback.user?.avatar?.url || feedback.user?.avatar;
    const username = feedback.user?.username || "Unknown";
    const createdAt = new Date(feedback.createdAt).toLocaleDateString("en-AU", {
        day: "2-digit",
        month: "short",
        year: "numeric",
    });

    return (
        <Card.Root
            bg="dark.800"
            color="whiteAlpha.900"
            rounded="xl"
            borderWidth="1px"
            borderColor={feedback.isPinned ? "brand.500" : "whiteAlpha.100"}
            position="relative"
            transition="border-color 0.2s ease, box-shadow 0.2s ease"
            boxShadow={feedback.isPinned ? "0 0 0 1px var(--chakra-colors-brand-500)" : "none"}
            _hover={{ borderColor: "whiteAlpha.300" }}
        >
            {/* Pinned indicator */}
            {feedback.isPinned && (
                <Box
                    position="absolute"
                    top={3}
                    right={3}
                    color="brand.400"
                    fontSize="xs"
                >
                    <FaThumbtack />
                </Box>
            )}

            <Card.Header pb={2}>
                <HStack gap={3} align="start">
                    <UserAvatar src={avatarUrl} name={username} size="sm" />
                    <VStack align="start" gap={0} flex={1} overflow="hidden">
                        <Text fontWeight="semibold" fontSize="sm" noOfLines={1}>
                            {username}
                        </Text>
                        <Text fontSize="xs" color="whiteAlpha.500">
                            {createdAt}
                        </Text>
                    </VStack>
                    <Spacer />
                    {feedback.rating > 0 && <StarRating rating={feedback.rating} />}
                </HStack>
            </Card.Header>

            <Card.Body py={2}>
                <Text fontSize="sm" color="whiteAlpha.800" lineHeight="tall">
                    {feedback.content}
                </Text>
            </Card.Body>

            <Card.Footer pt={2} borderTopWidth="1px" borderColor="whiteAlpha.100">
                <HStack w="full" justify="space-between">
                    {/* Pin toggle */}
                    <HStack gap={2} align="center">
                        <Box
                            as="button"
                            role="switch"
                            aria-checked={feedback.isPinned}
                            onClick={() => !isTogglingPin && onTogglePin(feedback.id)}
                            display="flex"
                            alignItems="center"
                            w="42px"
                            h="24px"
                            borderRadius="999px"
                            px="3px"
                            cursor={isTogglingPin ? "not-allowed" : "pointer"}
                            opacity={isTogglingPin ? 0.6 : 1}
                            style={{
                                background: feedback.isPinned ? "#62b731ff" : "rgba(255,255,255,0.15)",
                                transition: "background 0.25s ease",
                                flexShrink: 0,
                            }}
                        >
                            <Box
                                w="18px"
                                h="18px"
                                borderRadius="full"
                                bg="white"
                                boxShadow="0 1px 3px rgba(0,0,0,0.35)"
                                style={{
                                    transform: feedback.isPinned ? "translateX(18px)" : "translateX(0px)",
                                    transition: "transform 0.25s ease",
                                    flexShrink: 0,
                                }}
                            />
                        </Box>
                        <Text fontSize="xs" color="whiteAlpha.600">
                            {feedback.isPinned ? "Pinned" : "Unpinned"}
                        </Text>
                    </HStack>

                    {/* Delete */}
                    <IconButton
                        size="sm"
                        variant="ghost"
                        color="red.400"
                        _hover={{ bg: "red.900", color: "red.300" }}
                        aria-label="Delete feedback"
                        loading={isDeleting}
                        onClick={() => onDelete(feedback.id)}
                    >
                        <FaTrash />
                    </IconButton>
                </HStack>
            </Card.Footer>
        </Card.Root>
    );
}

export default FeedbackCard;

import {
    VStack,
    HStack,
    Heading,
    Center,
    Spinner,
    SimpleGrid,
    Text,
    Box,
    Pagination,
    IconButton,
    ButtonGroup,
} from "@chakra-ui/react";
import { LuChevronLeft, LuChevronRight, LuX } from "react-icons/lu";
import { FaThumbtack } from "react-icons/fa";
import useFeedbackAdmin from "@/hooks/feedbacks/useFeedbackAdmin";
import FeedbackCard from "@/components/common/cards/FeedbackCard";

const LIMIT_OPTIONS = [9, 15, 20, 50];

const filterSelectStyle = {
    background: "#1a1d26",
    border: "1px solid rgba(255,255,255,0.15)",
    color: "white",
    borderRadius: "8px",
    padding: "6px 12px",
    fontSize: "14px",
    cursor: "pointer",
};

function FeedbackPage() {
    const {
        feedbacks,
        pagination,
        isLoading,
        page,
        setPage,
        limit,
        setLimit,
        isPinned,
        setIsPinned,
        rating,
        setRating,
        togglePin,
        deleteFeedback,
        isTogglingPin,
        isDeleting,
    } = useFeedbackAdmin();

    return (
        <VStack gap={6} align="stretch">
            {/* Header row */}
            <HStack justify="space-between" align="center" flexWrap="wrap" gap={3}>
                <VStack align="start" gap={0}>
                    <Heading color="dark.500" fontSize="30px" fontWeight="bold">
                        Feedback Management
                    </Heading>
                    <Text fontSize="sm" color="whiteAlpha.500">
                        {pagination.total} total feedbacks
                    </Text>
                </VStack>

                {/* Controls */}
                <HStack gap={3} flexWrap="wrap">
                    {/* Pin filter */}
                    <select
                        style={filterSelectStyle}
                        value={isPinned === undefined ? "" : String(isPinned)}
                        onChange={(e) => {
                            const v = e.target.value;
                            setIsPinned(v === "" ? undefined : v === "true");
                        }}
                    >
                        <option value="" style={{ background: "#1a1d26" }}>
                            All Feedbacks
                        </option>
                        <option value="true" style={{ background: "#1a1d26" }}>
                            📌 Pinned Only
                        </option>
                        <option value="false" style={{ background: "#1a1d26" }}>
                            Unpinned Only
                        </option>
                    </select>

                    {/* Rating filter */}
                    <select
                        style={filterSelectStyle}
                        value={rating}
                        onChange={(e) => {
                            const v = e.target.value;
                            console.log(v);
                            setRating(v);
                        }}
                    >
                        <option value="0" style={{ background: "#1a1d26" }}>
                            All
                        </option>
                        <option value="1" style={{ background: "#1a1d26" }}>
                            ⭐️ (1 star)
                        </option>
                        <option value="2" style={{ background: "#1a1d26" }}>
                            ⭐️⭐️ (2 stars)
                        </option>
                        <option value="3" style={{ background: "#1a1d26" }}>
                            ⭐️⭐️⭐️ (3 stars)
                        </option>
                        <option value="4" style={{ background: "#1a1d26" }}>
                            ⭐️⭐️⭐️⭐️ (4 stars)
                        </option>
                        <option value="5" style={{ background: "#1a1d26" }}>
                            ⭐️⭐️⭐️⭐️⭐️ (5 stars)
                        </option>
                    </select>

                    {/* Items per page */}
                    <select
                        style={filterSelectStyle}
                        value={limit}
                        onChange={(e) => setLimit(Number(e.target.value))}
                    >
                        {LIMIT_OPTIONS.map((n) => (
                            <option key={n} value={n} style={{ background: "#1a1d26" }}>
                                {n} per page
                            </option>
                        ))}
                    </select>

                    {/* Clear filter */}
                    {isPinned !== undefined && (
                        <Box
                            as="button"
                            display="flex"
                            alignItems="center"
                            gap={1}
                            px={3}
                            py="6px"
                            borderRadius="8px"
                            fontSize="13px"
                            color="whiteAlpha.700"
                            style={{
                                border: "1px solid rgba(255,255,255,0.15)",
                                background: "transparent",
                                cursor: "pointer",
                            }}
                            onClick={() => setIsPinned(undefined)}
                        >
                            <LuX size={13} />
                            <Text as="span">Clear filter</Text>
                        </Box>
                    )}
                </HStack>
            </HStack>

            {/* Content */}
            {isLoading ? (
                <Center py={20}>
                    <Spinner size="xl" color="brand.500" />
                </Center>
            ) : feedbacks.length === 0 ? (
                <Center py={20} flexDirection="column" gap={3}>
                    <Box color="whiteAlpha.300" fontSize="4xl">
                        <FaThumbtack />
                    </Box>
                    <Text color="whiteAlpha.500" fontSize="md">
                        No feedbacks found
                    </Text>
                </Center>
            ) : (
                <SimpleGrid columns={{ base: 1, md: 2, lg: 3 }} gap={4}>
                    {feedbacks.map((fb) => (
                        <FeedbackCard
                            key={fb.id}
                            feedback={fb}
                            onTogglePin={togglePin}
                            onDelete={deleteFeedback}
                            isTogglingPin={isTogglingPin}
                            isDeleting={isDeleting}
                        />
                    ))}
                </SimpleGrid>
            )}

            {/* Pagination */}
            {pagination.totalPages > 1 && (
                <Center mt={4}>
                    <Pagination.Root
                        count={pagination.total}
                        pageSize={pagination.limit}
                        page={page}
                        onPageChange={(e) => setPage(e.page)}
                    >
                        <ButtonGroup variant="ghost" size="sm" wrap="wrap">
                            <Pagination.PrevTrigger asChild>
                                <IconButton>
                                    <LuChevronLeft />
                                </IconButton>
                            </Pagination.PrevTrigger>

                            <Pagination.Items
                                render={(pageObj) => (
                                    <IconButton
                                        variant={pageObj.value === page ? "solid" : "ghost"}
                                        bg={pageObj.value === page ? "brand.500" : "transparent"}
                                        color={pageObj.value === page ? "white" : "inherit"}
                                        borderRadius="full"
                                    >
                                        {pageObj.value}
                                    </IconButton>
                                )}
                            />

                            <Pagination.NextTrigger asChild>
                                <IconButton>
                                    <LuChevronRight />
                                </IconButton>
                            </Pagination.NextTrigger>
                        </ButtonGroup>
                    </Pagination.Root>
                </Center>
            )}
        </VStack>
    );
}

export default FeedbackPage;

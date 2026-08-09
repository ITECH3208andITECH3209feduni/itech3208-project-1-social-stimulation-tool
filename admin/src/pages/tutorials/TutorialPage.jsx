import {
    VStack,
    HStack,
    Pagination,
    IconButton,
    ButtonGroup,
    Heading,
    Spinner,
    Center,
    Box,
    Text,
    NativeSelect,
} from "@chakra-ui/react";
import { LuChevronLeft, LuChevronRight, LuX } from "react-icons/lu";
import VideoGrid from "@/components/common/grids/VideoGrid";
import useVideo from "@/hooks/common/useVideo";
import { useQueryClient } from "@tanstack/react-query";
import { videoQueries } from "@/api/videos/video.queries";
import useUpdateVideo from "@/hooks/videos/useUpdateVideo";
import useDeleteVideo from "@/hooks/videos/useDeleteVideo";
import VideoDetailDrawer from "@/components/common/drawers/VideoDetailDrawer";
import VideoDeleteDialog from "@/components/common/dialogs/VideoDeleteDialog";
import useCategories from "@/hooks/common/useCategories";
import useSubCategories from "@/hooks/common/useSubCategories";

function TutorialPage() {
    const queryClient = useQueryClient();
    const {
        page, setPage,
        limit, status,
        categoryId, setCategoryId,
        subCategoryId, setSubCategoryId,
        videos, pagination, isLoading,
    } = useVideo({ initialLimit: 12 });

    const { isOpen: isUpdateOpen, onClose: closeUpdate, handleUpdate, video: updateTargetVideo } = useUpdateVideo();
    const { isOpen: isDeleteOpen, onClose: closeDelete, handleDelete, video: deleteTargetVideo, isDeleting } = useDeleteVideo();

    const { categories } = useCategories();
    const { subCategories } = useSubCategories(categoryId);

    const handlePrefetchPage = (targetNumber) => {
        if (targetNumber < 1 || targetNumber > pagination.totalPages) return;
        queryClient.prefetchQuery(videoQueries.list({ page: targetNumber, limit, status, categoryId, subCategoryId }));
    };

    const selectStyle = {
        background: "#1a1d26",
        border: "1px solid rgba(255,255,255,0.15)",
        color: "white",
        borderRadius: "8px",
        padding: "6px 12px",
        fontSize: "14px",
        cursor: "pointer",
        minWidth: "180px",
    };

    return (
        <VStack gap={6} align="stretch">
            {/* Header row */}
            <HStack justify="space-between" align="center" flexWrap="wrap" gap={3}>
                <Heading color="dark.500" fontSize="30px" fontWeight="bold">
                    Video Management
                </Heading>

                {/* Filter controls */}
                <HStack gap={3} flexWrap="wrap">
                    {/* Category filter */}
                    <Box position="relative">
                        <select
                            style={selectStyle}
                            value={categoryId}
                            onChange={(e) => setCategoryId(e.target.value)}
                        >
                            <option value="" style={{ background: "#1a1d26" }}>All Categories</option>
                            {categories.map((cat) => (
                                <option key={cat.id} value={cat.id} style={{ background: "#1a1d26" }}>
                                    {cat.name}
                                </option>
                            ))}
                        </select>
                    </Box>

                    {/* Sub-category filter — only shown when a category is active */}
                    {categoryId && (
                        <Box>
                            <select
                                style={selectStyle}
                                value={subCategoryId}
                                onChange={(e) => setSubCategoryId(e.target.value)}
                            >
                                <option value="" style={{ background: "#1a1d26" }}>
                                    {subCategories.length === 0 ? "No sub-categories" : "All Sub-Categories"}
                                </option>
                                {subCategories.map((sub) => (
                                    <option key={sub.id} value={sub.id} style={{ background: "#1a1d26" }}>
                                        {sub.name}
                                    </option>
                                ))}
                            </select>
                        </Box>
                    )}

                    {/* Clear filters badge */}
                    {(categoryId || subCategoryId) && (
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
                            style={{ border: "1px solid rgba(255,255,255,0.15)", background: "transparent", cursor: "pointer" }}
                            _hover={{ color: "white" }}
                            onClick={() => { setCategoryId(""); setSubCategoryId(""); }}
                        >
                            <LuX size={13} />
                            <Text as="span">Clear filters</Text>
                        </Box>
                    )}
                </HStack>
            </HStack>

            {isLoading ? (
                <Center py={10}>
                    <Spinner size="xl" color="brand.500" />
                </Center>
            ) : (
                <VideoGrid videos={videos} />
            )}

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
                                <IconButton onMouseEnter={() => handlePrefetchPage(page - 1)}>
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
                                        onMouseEnter={() => handlePrefetchPage(pageObj.value)}
                                    >
                                        {pageObj.value}
                                    </IconButton>
                                )}
                            />

                            <Pagination.NextTrigger asChild>
                                <IconButton onMouseEnter={() => handlePrefetchPage(page + 1)}>
                                    <LuChevronRight />
                                </IconButton>
                            </Pagination.NextTrigger>
                        </ButtonGroup>
                    </Pagination.Root>
                </Center>
            )}

            <VideoDetailDrawer
                isOpen={isUpdateOpen}
                onClose={closeUpdate}
                onSave={handleUpdate}
                video={updateTargetVideo}
            />

            <VideoDeleteDialog
                isOpen={isDeleteOpen}
                onClose={closeDelete}
                onSave={handleDelete}
                video={deleteTargetVideo}
                isDeleting={isDeleting}
            />
        </VStack>
    );
}

export default TutorialPage;


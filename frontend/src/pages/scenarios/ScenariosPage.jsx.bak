import {
    Box,
    Flex,
    Heading,
    Text,
    HStack,
    VStack,
    Grid,
    GridItem,
    Center,
    Spinner,
    ButtonGroup,
    IconButton,
    Pagination,
} from "@chakra-ui/react";
import { LuChevronLeft, LuChevronRight } from "react-icons/lu";
import { videoQueries } from "@/api/videos/video.queries";
import VideoCard from "@/components/common/cards/VideoCard";
import SelectionField from "@/components/common/fields/SelectionField";
import ClearFilterBadge from "@/components/common/filters/ClearFilterBadge";
import { useQueryClient } from "@tanstack/react-query";
import useCategories from "@/hooks/custom-hooks/useCategories";
import useSubCategories from "@/hooks/custom-hooks/useSubCategories";
import useVideo from "@/hooks/custom-hooks/useVideo";

function ScenariosPage() {
    const queryClient = useQueryClient();
    const {
        page,
        setPage,
        limit,
        status,
        categoryId,
        setCategoryId,
        subCategoryId,
        setSubCategoryId,
        clearFilters,
        videos,
        pagination,
        isLoading,
    } = useVideo({ initialLimit: 12 });

    const { categories } = useCategories();
    const { subCategories } = useSubCategories(categoryId);
    const selectedCategory = categories.find((category) => category.id === categoryId);
    const selectedSubCategory = subCategories.find((subCategory) => subCategory.id === subCategoryId);
    const selectedFilterName = selectedSubCategory?.name || selectedCategory?.name || "all categories";

    const handlePrefetchPage = (targetNumber) => {
        if (targetNumber < 1 || targetNumber > pagination.totalPages) return;
        queryClient.prefetchQuery(
            videoQueries.list({ page: targetNumber, limit, status, categoryId, subCategoryId }),
        );
    };

    return (
        <Flex
            w={"100%"}
            p="8px"
            align={"center"}
            justify={"center"}
            direction={"column"}
            bg={"white"}
        >
            <Heading
                fontFamily={"Sora"}
                fontSize={48}
                color={"brand.500"}
                fontWeight={"bold"}
                mb={6}
            >
                Welcome to scenario videos
            </Heading>

            <VStack w="full" maxW="1200px" mx="auto" gap={6}>
                {/* Filter controls */}
                <HStack
                    w="full"
                    align={{ base: "stretch", md: "center" }}
                    justify="space-between"
                    flexDirection={{ base: "column", md: "row" }}
                    gap={3}
                >
                    <Text color="gray.600" fontSize="sm" fontWeight="medium">
                        {isLoading
                            ? "Loading videos…"
                            : `${pagination.total} ${pagination.total === 1 ? "video" : "videos"} in ${selectedFilterName}`}
                    </Text>

                    <HStack gap={3} flexWrap="wrap" justify={{ base: "flex-start", md: "flex-end" }}>
                        {/* Category filter */}
                        <Box position="relative">
                            <SelectionField
                                h="40px"
                                value={categoryId}
                                onChange={(e) => setCategoryId(e.target.value)}
                                inputPlaceholder="All Categories"
                                items={categories}
                                inputColor="#1a1d26"
                                borderColor="rgba(255,255,255,0.15)"
                            />
                        </Box>

                        {/* Sub-category filter — only shown when a category is active */}
                        {categoryId && (
                            <Box position="relative">
                                <SelectionField
                                    h="40px"
                                    value={subCategoryId}
                                    onChange={(e) => setSubCategoryId(e.target.value)}
                                    inputPlaceholder={
                                        subCategories.length === 0
                                            ? "No sub-categories"
                                            : "All Sub-Categories"
                                    }
                                    items={subCategories}
                                    inputColor="#1a1d26"
                                    borderColor="rgba(255,255,255,0.15)"
                                />
                            </Box>
                        )}

                        {/* Clear filters badge */}
                        {(status || categoryId || subCategoryId) && (
                            <ClearFilterBadge h="40px" onClick={clearFilters} />
                        )}
                    </HStack>
                </HStack>

                {/* Video Grid */}
                {isLoading ? (
                    <Center py={10}>
                        <Spinner size="xl" color="brand.500" />
                    </Center>
                ) : videos.length === 0 ? (
                    <Center py={10}>
                        <Text color="gray.500">No videos found.</Text>
                    </Center>
                ) : (
                    <Grid
                        w="full"
                        templateColumns={{
                            base: "1fr",
                            md: "repeat(2, 1fr)",
                            lg: "repeat(3, 1fr)",
                        }}
                        gap={6}
                    >
                        {videos.map((video) => (
                            <GridItem key={video.id}>
                                <VideoCard video={video} />
                            </GridItem>
                        ))}
                    </Grid>
                )}

                {/* Pagination */}
                {!isLoading && pagination.totalPages > 1 && (
                    <Box mt={8}>
                        <Pagination.Root
                            count={pagination.total}
                            pageSize={limit}
                            defaultPage={page}
                            page={page}
                            onPageChange={(e) => setPage(e.page)}
                        >
                            <ButtonGroup variant="ghost" size="sm">
                                <Pagination.PrevTrigger asChild>
                                    <IconButton>
                                        <LuChevronLeft />
                                    </IconButton>
                                </Pagination.PrevTrigger>

                                <Pagination.Items
                                    render={(item) => (
                                        <IconButton
                                            variant={{ base: "ghost", _selected: "outline" }}
                                            onMouseEnter={() => handlePrefetchPage(item.value)}
                                        >
                                            {item.value}
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
                    </Box>
                )}
            </VStack>
        </Flex>
    );
}

export default ScenariosPage;

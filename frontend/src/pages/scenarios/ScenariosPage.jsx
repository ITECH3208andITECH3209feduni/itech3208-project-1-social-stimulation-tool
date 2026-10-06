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
    Image,
    Badge,
    Button,
} from "@chakra-ui/react";
import { LuChevronLeft, LuChevronRight } from "react-icons/lu";
import { videoQueries } from "@/api/videos/video.queries";
import SelectionField from "@/components/common/fields/SelectionField";
import ClearFilterBadge from "@/components/common/filters/ClearFilterBadge";
import { useQueryClient } from "@tanstack/react-query";
import useCategories from "@/hooks/custom-hooks/useCategories";
import useSubCategories from "@/hooks/custom-hooks/useSubCategories";
import useVideo from "@/hooks/custom-hooks/useVideo";
import { useState, useEffect } from "react";
import ReactPlayer from "react-player";

function ScenariosPage() {
    const queryClient = useQueryClient();
    const {
        status,
        categoryId,
        setCategoryId,
        subCategoryId,
        setSubCategoryId,
        clearFilters,
        videos,
        pagination,
        isLoading,
    } = useVideo({ initialLimit: 100 });

    const [currentVideoIndex, setCurrentVideoIndex] = useState(0);
    const [maxUnlockedIndex, setMaxUnlockedIndex] = useState(0);
    const [isVideoEnded, setIsVideoEnded] = useState(false);
    const [isAutoPlay, setIsAutoPlay] = useState(false);

    useEffect(() => {
        setCurrentVideoIndex(0);
        setMaxUnlockedIndex(0);
        setIsVideoEnded(false);
        setIsAutoPlay(false);
    }, [videos, categoryId, subCategoryId]);

    const { categories } = useCategories();
    const { subCategories } = useSubCategories(categoryId);
    const selectedCategory = categories.find((category) => category.id === categoryId);
    const selectedSubCategory = subCategories.find(
        (subCategory) => subCategory.id === subCategoryId,
    );
    const selectedFilterName =
        selectedSubCategory?.name || selectedCategory?.name || "all categories";

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

                    <HStack
                        gap={3}
                        flexWrap="wrap"
                        justify={{ base: "flex-start", md: "flex-end" }}
                    >
                        {/* Category filter */}
                        <Box position="relative">
                            <SelectionField
                                h="40px"
                                value={categoryId}
                                onChange={(e) => setCategoryId(e.target.value)}
                                inputPlaceholder="All Categories"
                                items={categories}
                                inputColor="whiteAlpha.500"
                                borderColor="#1a1d26"
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
                                    inputColor="whiteAlpha.500"
                                    borderColor="#1a1d26"
                                />
                            </Box>
                        )}

                        {/* Clear filters badge */}
                        {(status || categoryId || subCategoryId) && (
                            <ClearFilterBadge
                                h="40px"
                                bg="whiteAlpha.500"
                                color="black"
                                borderColor="#1a1d26"
                                onClick={clearFilters}
                            />
                        )}
                    </HStack>
                </HStack>

                {/* Playlist Layout */}
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
                            lg: "6.5fr 3.5fr",
                        }}
                        gap={2}
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
                    </Box>
                )}
            </VStack>
        </Flex>
    );
}

export default ScenariosPage;

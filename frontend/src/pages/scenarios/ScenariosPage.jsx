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
    const selectedSubCategory = subCategories.find((subCategory) => subCategory.id === subCategoryId);
    const selectedFilterName = selectedSubCategory?.name || selectedCategory?.name || "all categories";

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
                            lg: "7fr 3fr",
                        }}
                        gap={6}
                    >
                        {/* Main Video Player */}
                        <GridItem>
                            {videos[currentVideoIndex] && (
                                <VStack align="start" w="full" bg="dark.900" rounded="xl" overflow="hidden" boxShadow="xl" gap={0}>
                                    <Box position="relative" bg="black" w="full" aspectRatio={16 / 9}>
                                        {!isVideoEnded ? (
                                            <ReactPlayer
                                                src={typeof videos[currentVideoIndex].video === "object" ? videos[currentVideoIndex].video?.url : videos[currentVideoIndex].video}
                                                playing={isAutoPlay}
                                                controls
                                                width="100%"
                                                height="100%"
                                                onEnded={() => {
                                                    setIsVideoEnded(true);
                                                    setMaxUnlockedIndex((prev) => Math.max(prev, currentVideoIndex + 1));
                                                }}
                                                config={{
                                                    file: {
                                                        attributes: {
                                                            controlsList: "nodownload",
                                                            disablePictureInPicture: false,
                                                        },
                                                    },
                                                }}
                                            />
                                        ) : (
                                            <Center w="full" h="full" bg="blackAlpha.800" flexDirection="column" gap={6} textAlign="center" px={4}>
                                                {currentVideoIndex < videos.length - 1 ? (
                                                    <>
                                                        <VStack gap={2}>
                                                            <Text color="gray.400" fontSize="sm" fontWeight="bold" textTransform="uppercase" letterSpacing="wider">Up Next</Text>
                                                            <Heading color="white" size="md" noOfLines={2}>{videos[currentVideoIndex + 1].title}</Heading>
                                                        </VStack>
                                                        <HStack gap={4} mt={2}>
                                                            <Button 
                                                                variant="outline"
                                                                color="white" 
                                                                borderColor="whiteAlpha.400"
                                                                size="lg" 
                                                                _hover={{ bg: "whiteAlpha.200" }} 
                                                                onClick={() => {
                                                                    setIsVideoEnded(false);
                                                                    setIsAutoPlay(true);
                                                                }}
                                                            >
                                                                Replay
                                                            </Button>
                                                            <Button 
                                                                bg="brand.500" 
                                                                color="white" 
                                                                size="lg" 
                                                                _hover={{ bg: "brand.600" }} 
                                                                onClick={() => {
                                                                    setCurrentVideoIndex(currentVideoIndex + 1);
                                                                    setIsVideoEnded(false);
                                                                    setIsAutoPlay(true);
                                                                }}
                                                            >
                                                                Play Next
                                                            </Button>
                                                        </HStack>
                                                    </>
                                                ) : (
                                                    <>
                                                        <Heading color="white" size="lg">You've finished the playlist!</Heading>
                                                        <HStack gap={4} mt={2}>
                                                            <Button 
                                                                variant="outline"
                                                                color="white" 
                                                                borderColor="whiteAlpha.400"
                                                                size="lg" 
                                                                _hover={{ bg: "whiteAlpha.200" }} 
                                                                onClick={() => {
                                                                    setIsVideoEnded(false);
                                                                    setIsAutoPlay(true);
                                                                }}
                                                            >
                                                                Replay Video
                                                            </Button>
                                                            <Button 
                                                                bg="brand.500" 
                                                                color="white" 
                                                                size="lg" 
                                                                _hover={{ bg: "brand.600" }} 
                                                                onClick={() => {
                                                                    setCurrentVideoIndex(0);
                                                                    setIsVideoEnded(false);
                                                                    setIsAutoPlay(true);
                                                                }}
                                                            >
                                                                Restart Playlist
                                                            </Button>
                                                        </HStack>
                                                    </>
                                                )}
                                            </Center>
                                        )}
                                    </Box>
                                    <Box p={5} w="full" bg="gray.800">
                                        <Heading color="white" size="lg" mb={2}>
                                            {videos[currentVideoIndex].title}
                                        </Heading>
                                        <Text color="gray.300" fontSize="md">
                                            {videos[currentVideoIndex].description}
                                        </Text>
                                        <HStack mt={4} gap={2} flexWrap="wrap">
                                            {videos[currentVideoIndex].category?.name && (
                                                <Badge bg="blue.500" color="white" px={2} py={1} borderRadius="md">
                                                    {videos[currentVideoIndex].category.name}
                                                </Badge>
                                            )}
                                            {videos[currentVideoIndex].subCategory?.name && (
                                                <Badge bg="purple.500" color="white" px={2} py={1} borderRadius="md">
                                                    {videos[currentVideoIndex].subCategory.name}
                                                </Badge>
                                            )}
                                        </HStack>
                                    </Box>
                                </VStack>
                            )}
                        </GridItem>

                        {/* Playlist Sidebar */}
                        <GridItem position="relative" minH={{ base: "400px", lg: "auto" }}>
                            <Box 
                                position={{ base: "relative", lg: "absolute" }} 
                                top={0} bottom={0} left={0} right={0}
                                h="100%"
                            >
                                <VStack 
                                    w="full" 
                                    h="100%" 
                                    overflowY="auto" 
                                    bg="gray.50" 
                                    rounded="xl" 
                                    p={4} 
                                    align="stretch"
                                    gap={3}
                                    boxShadow="md"
                                >
                                <Heading size="md" color="brand.500" pb={2} borderBottom="1px solid" borderColor="gray.200">
                                    Playlist
                                </Heading>
                                {videos.map((vid, idx) => {
                                    const isActive = idx === currentVideoIndex;
                                    const isUnlocked = idx <= maxUnlockedIndex;
                                    const rawThumbnail = typeof vid.thumbnail === "object" ? vid.thumbnail?.url : vid.thumbnail;
                                    const videoUrl = typeof vid.video === "object" ? vid.video?.url : vid.video;
                                    const thumbnailUrl = rawThumbnail || (videoUrl ? videoUrl.replace(/\.[^/.]+$/, ".jpg") : "");

                                    return (
                                        <HStack 
                                            key={vid.id} 
                                            p={2} 
                                            bg={isActive ? "white" : "transparent"} 
                                            shadow={isActive ? "md" : "none"}
                                            rounded="md"
                                            opacity={isUnlocked ? 1 : 0.6}
                                            border={isActive ? "2px solid" : "1px solid"}
                                            borderColor={isActive ? "brand.500" : "transparent"}
                                            cursor={isUnlocked ? "pointer" : "not-allowed"}
                                            transition="all 0.2s"
                                            alignItems="flex-start"
                                            onClick={() => {
                                                if (isUnlocked) {
                                                    setCurrentVideoIndex(idx);
                                                    setIsVideoEnded(false);
                                                    setIsAutoPlay(true);
                                                }
                                            }}
                                            _hover={isUnlocked && !isActive ? { bg: "gray.100" } : {}}
                                        >
                                            <Box position="relative" w="120px" flexShrink={0}>
                                                <Image 
                                                    src={thumbnailUrl} 
                                                    w="100%" 
                                                    aspectRatio={16/9} 
                                                    objectFit="cover" 
                                                    rounded="sm" 
                                                    alt={vid.title}
                                                />
                                            </Box>
                                            <VStack align="start" gap={1} flex={1}>
                                                <Text fontSize="sm" fontWeight={isActive ? "bold" : "medium"} color={isActive ? "brand.600" : "gray.700"} noOfLines={2}>
                                                    {vid.title}
                                                </Text>
                                                <Text fontSize="xs" color="gray.500">
                                                    {isActive ? (isVideoEnded ? "Finished" : "Playing now") : (isUnlocked ? "Available" : "Locked")}
                                                </Text>
                                            </VStack>
                                        </HStack>
                                    )
                                })}
                            </VStack>
                            </Box>
                        </GridItem>
                    </Grid>
                )}
            </VStack>
        </Flex>
    );
}

export default ScenariosPage;

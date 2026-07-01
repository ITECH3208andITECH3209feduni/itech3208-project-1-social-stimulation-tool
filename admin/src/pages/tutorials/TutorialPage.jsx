import {
    VStack,
    Pagination,
    IconButton,
    ButtonGroup,
    Heading,
    Spinner,
    Center,
} from "@chakra-ui/react";
import { LuChevronLeft, LuChevronRight } from "react-icons/lu";
import VideoGrid from "@/components/common/grids/VideoGrid";
import useVideo from "@/hooks/common/useVideo";
import { useQueryClient } from "@tanstack/react-query";
import { videoQueries } from "@/api/videos/video.queries";
import { useSearchParams } from "react-router-dom";

function TutorialPage() {
    const queryClient = useQueryClient();
    const { page, setPage, limit, status, categoryId, videos, pagination, isLoading } = useVideo({
        initialLimit: 12,
    });

    const handlePrefetchPage = (targetNumber) => {
        if (targetNumber < 1 || targetNumber > pagination.totalPages) return;

        queryClient.prefetchQuery(videoQueries.list({ page: targetNumber, limit, status, categoryId }));
    };

    return (
        <VStack gap={6} align="stretch">
            <Heading alignSelf={"start"} color="dark.500" fontSize={"30px"} fontWeight="bold">
                Video Management
            </Heading>

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
                                        borderRadius={"full"}
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
        </VStack>
    );
}

export default TutorialPage;

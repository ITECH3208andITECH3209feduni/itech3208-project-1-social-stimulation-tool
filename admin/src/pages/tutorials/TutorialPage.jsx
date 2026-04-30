import { VStack, Pagination, IconButton, ButtonGroup, Heading } from "@chakra-ui/react";
import { LuChevronLeft, LuChevronRight } from "react-icons/lu";
import VideoGrid, { videoList } from "@/components/common/grids/VideoGrid";

function TutorialPage() {
    return (
        <VStack gap={6}>
            <Heading alignSelf={"start"} color={"brand.400"} fontSize={48}>
                Tutorial Management
            </Heading>
            <VideoGrid />
            {/* Pagination */}
            <Pagination.Root count={videoList.length * 5} pageSize={5} page={1}>
                <ButtonGroup variant="ghost" size="sm" wrap="wrap">
                    <Pagination.PrevTrigger asChild>
                        <IconButton>
                            <LuChevronLeft />
                        </IconButton>
                    </Pagination.PrevTrigger>

                    <Pagination.Items
                        render={(page) => (
                            <IconButton variant={{ base: "ghost", _selected: "outline" }}>
                                {page.value}
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
        </VStack>
    );
}

export default TutorialPage;

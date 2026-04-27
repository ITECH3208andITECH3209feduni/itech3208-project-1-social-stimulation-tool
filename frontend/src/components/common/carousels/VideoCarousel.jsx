import { Box, Carousel, IconButton, Float, Button, Flex } from "@chakra-ui/react";
import { LuChevronLeft, LuChevronRight } from "react-icons/lu";
import { FiPlus } from "react-icons/fi";
import VideoCard from "../cards/VideoCard";

const items = Array.from({ length: 5 });

function VideoCarousel() {
    return (
        <Carousel.Root
            maxW="100%"
            maxH="1000px"
            mx="auto"
            overflow={"hidden"}
            loop
            autoplay
            slideCount={items.length}
        >
            <Carousel.Control justifyContent="center" gap="4">
                {/* Previous Button */}
                <Float placement={"middle-start"} offsetX={"5"}>
                    <Carousel.PrevTrigger asChild>
                        <IconButton
                            size={"md"}
                            variant={"solid"}
                            color={"white"}
                            background={"whiteAlpha.300"}
                        >
                            <LuChevronLeft />
                        </IconButton>
                    </Carousel.PrevTrigger>
                </Float>

                {/* List Items */}
                <Carousel.ItemGroup>
                    {items.map((_, index) => {
                        return (
                            <Carousel.Item key={index} index={index} h={"100%"}>
                                <VideoCard />
                            </Carousel.Item>
                        );
                    })}
                </Carousel.ItemGroup>

                {/* Next Button */}
                <Float placement={"middle-end"} offsetX={"5"}>
                    <Carousel.NextTrigger asChild>
                        <IconButton
                            size={"md"}
                            variant={"solid"}
                            color={"white"}
                            background={"whiteAlpha.300"}
                        >
                            <LuChevronRight />
                        </IconButton>
                    </Carousel.NextTrigger>
                </Float>
            </Carousel.Control>

            {/* Indicators */}
            <Float w={"100%"} placement={"bottom-center"} offsetY={"5"} p={6}>
                <Flex w={"100%"} justify={"space-between"} align={"center"} mb={10}>
                    <Button color={"white"} bg={"whiteAlpha.500"}>
                        <FiPlus />
                        Add to Wishlist
                    </Button>
                    <Box padding={"5px"} bg={"whiteAlpha.500"} rounded={"sm"}>
                        <Carousel.Indicators
                            bg={"white"}
                            _current={{
                                bg: "brand.500", // Color when active
                            }}
                        />
                    </Box>
                    <Button color={"white"} bg={"brand.500"} fontFamily={"Sora"}>
                        Watch Now
                    </Button>
                </Flex>
            </Float>
        </Carousel.Root>
    );
}

export default VideoCarousel;

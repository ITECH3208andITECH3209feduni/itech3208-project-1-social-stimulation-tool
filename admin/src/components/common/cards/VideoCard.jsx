import {
    Image,
    Card,
    HStack,
    VStack,
    Button,
    Box,
    Float,
    IconButton,
    Text,
    Spacer,
} from "@chakra-ui/react";
import { FaPlay, FaComment, FaEye } from "react-icons/fa";

function VideoCard({ video }) {
    return (
        <VStack bg={"dark.800"} rounded={"md"} overflow={"hidden"}>
            <Box position={"relative"}>
                <Image
                    flex={1}
                    w={"100%"}
                    h={"auto"}
                    aspectRatio={16 / 9}
                    src={video.thumbnail}
                    objectFit={"cover"}
                />
                <Float placement={"middle-center"}>
                    <IconButton
                        size={"2xl"}
                        color={"whiteAlpha.900"}
                        bg={"brand.300"}
                        rounded={"full"}
                    >
                        <FaPlay display={"flex"} />
                    </IconButton>
                </Float>
                <Float placement={"bottom-end"} offsetX={12} offsetY={4}>
                    <Text>{video.duration}</Text>
                </Float>
            </Box>
            <Card.Root flex={1} w={"100%"}>
                <Card.Body gap="2" color={"whiteAlpha.900"}>
                    <Card.Title fontSize={25}>{video.title}</Card.Title>
                    <Card.Description color={"gray.400"}>{video.description}</Card.Description>
                </Card.Body>
                <Card.Footer>
                    <HStack w={"100%"} justify={"end"}>
                        <VStack align={"start"} >
                            <HStack color={"skyblue.300"} fontSize={18}>
                                {video.comments} Comments <FaComment />
                            </HStack>
                            <HStack color={"brand.300"}>
                                {video.comments} Views <FaEye />
                            </HStack>
                        </VStack>
                        <Spacer />
                        <Button w={100} color={"whiteAlpha.900"} bg={"skyblue.300"}>
                            Edit
                        </Button>
                        <Button w={100} color={"whiteAlpha.900"} bg={"brand.300"}>
                            Delete
                        </Button>
                    </HStack>
                </Card.Footer>
            </Card.Root>
        </VStack>
    );
}

export default VideoCard;

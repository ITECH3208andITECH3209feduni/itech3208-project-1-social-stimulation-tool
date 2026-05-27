import { Avatar, Card, HStack, VStack } from "@chakra-ui/react";
import { MdVerified } from "react-icons/md";
import RatingBar from "../ratingbar/RatingBar";

function FeedbackCard({ name, rate, comment }) {
    return (
        <Card.Root borderColor={"gray.500"}>
            <Card.Body>
                <HStack gap={4}>
                    <Avatar.Root>
                        <Avatar.Image />
                    </Avatar.Root>
                    <VStack alignItems={"start"} gap={"-1"}>
                        <HStack>
                            <Card.Title>{name}</Card.Title>
                            <MdVerified color="green" />
                        </HStack>
                        <RatingBar rate={rate} />
                    </VStack>
                </HStack>
                <Card.Description>&ldquo;{comment}&rdquo;</Card.Description>
            </Card.Body>
        </Card.Root>
    );
}

export default FeedbackCard;

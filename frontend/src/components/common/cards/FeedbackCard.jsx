import { Avatar, Card, HStack, VStack } from "@chakra-ui/react";
import { MdVerified } from "react-icons/md";
import RatingBar from "../ratingbar/RatingBar";

function FeedbackCard() {
    return (
        <Card.Root borderColor={"gray.500"}>
            <Card.Body>
                <HStack gap={4}>
                    <Avatar.Root>
                        <Avatar.Image />
                    </Avatar.Root>
                    <VStack alignItems={"start"} gap={"-1"}>
                        <HStack>
                            <Card.Title>Alex. F</Card.Title>
                            <MdVerified color="green" />
                        </HStack>
                        <RatingBar rate={3} />
                    </VStack>
                </HStack>
                <Card.Description>
                    "Finding clothes that align with my personal style used to be a challenge until
                    I discovered Shop.co. The range of options they offer is truly remarkable,
                    catering to a variety of tastes and occasions.”
                </Card.Description>
            </Card.Body>
        </Card.Root>
    );
}

export default FeedbackCard;

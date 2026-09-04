import { Grid, GridItem } from "@chakra-ui/react";
import FeedbackCard from "../cards/FeedbackCard";
import FeedbackCardSkeleton from "../cards/FeedbackCardSkeleton";

function UsersFeedbackGrid({ feedbacks, loading }) {
    if (loading) {
        return (
            <Grid gap={8} templateColumns="repeat(3, 1fr)">
                <FeedbackCardSkeleton />
                <FeedbackCardSkeleton />
                <FeedbackCardSkeleton />
            </Grid>
        );
    }

    return (
        <Grid gap={8} templateColumns="repeat(3, 1fr)">
            {feedbacks.map((feedback) => (
                <GridItem key={feedback.id}>
                    <FeedbackCard feedback={feedback} />
                </GridItem>
            ))}
        </Grid>
    );
}

export default UsersFeedbackGrid;

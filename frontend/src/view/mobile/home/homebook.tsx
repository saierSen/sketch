import * as React from 'react';
import { Page, TabCard, Card, Slider } from '../../components/common';
import { API, ResData, ReqData } from '../../../config/api';
import { Tags } from '../../components/book/tags';
import { HomeNav } from './nav';
import { MobileRouteProps } from '../router';
import { RecomPreview } from '../../components/post/recom-preview';
import { PostPreview } from '../../components/post/post-preview';
import { BookPreview } from '../../components/book/book-preview';

interface State {
    data:API.Get['/homebook'];
    tags:API.Get['/config/noTongrenTags'];
}

export class HomeBook extends React.Component<MobileRouteProps, State> {
    public state:State = {
        data: {
            recent_long_recommendations: [],
            recent_short_recommendations: [],
            random_short_recommendations: [],
            recent_custom_short_recommendations: [],
            recent_custom_long_recommendations: [],
            recent_added_chapter_books: [],
            recent_responded_books: [],
            highest_jifen_books: [],
            most_collected_books: [],
        },
        tags: [],
    };

    public componentDidMount () {
        this.loadData();
    }

    public render () {
        return (<Page className="books" nav={<HomeNav />}>
            <Tags
                tags={this.state.tags}
                redirectPathname="/books"
                search={(pathname, tags) => {
                    this.props.core.history.push(pathname, {tags});
                }}
                getFullList={() => {
                    this.loadNoTongrenTags();
                }} />

            <TabCard
                tabs={[
                    {
                        name: '长推',
                        children: this.state.data.recent_long_recommendations.map(this.renderPostPreview),
                    },
                    {
                        name: '最新',
                        children: this.state.data.recent_short_recommendations.map(this.renderPostPreview),
                    },
                    {
                        name: '往期',
                        children: this.state.data.random_short_recommendations.map(this.renderPostPreview)
                    },
                ]}
            />
            
            <Card>
                <Slider morePath={''}>
                    {this.state.data.recent_custom_short_recommendations.map(data => this.renderRecomPreivew(data, data.id))}
                </Slider>
            </Card>

            <TabCard
                tabs={[
                    {
                        name: '最新更新',
                        children: this.state.data.recent_added_chapter_books.map(this.renderBookPreview),
                        more: `/books?ordered=${ReqData.Thread.ordered.latest_added_component}`,
                    },
                    {
                        name: '最高积分',
                        children: this.state.data.highest_jifen_books.map(this.renderBookPreview),
                        more: `/books?ordered=${ReqData.Thread.ordered.jifen}`,
                    },
                    {
                        name: '最多收藏',
                        children: this.state.data.most_collected_books.map(this.renderBookPreview),
                        more: `/books?ordered=${ReqData.Thread.ordered.collection_count}`,
                    },
                    {
                        name: '最新回复',
                        children: this.state.data.recent_responded_books.map(this.renderBookPreview),
                        more: `/books`,
                    } 
                ]}
            />
        </Page>);
    }

    public renderRecomPreivew = (data:ResData.Post, key:number) => {
        return <RecomPreview data={data} key={key} />;
    }

    public renderPostPreview = (data:ResData.Post) => {
        return <PostPreview data={data} />;
    }

    public renderBookPreview = (data:ResData.Thread) => {
        return <BookPreview data={data} />;
    }

    public loadData (tags?:number[]) {
        (async () => {
            const data = await this.props.core.db.getPageHomeBook();
            if (data) {
                this.setState({data});
            }
        })();
    }

    public loadNoTongrenTags () {
        (async () => {
            const tags = await this.props.core.db.getNoTongrenTags();
            if (tags) {
                this.setState({tags});
            }
        })();
    }
}
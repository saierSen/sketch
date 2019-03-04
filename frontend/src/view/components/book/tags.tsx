import * as React from 'react';
import { ResData } from '../../../config/api';
import { Card } from '../common';
import { indexEq } from '../../../utils/id';
import { removeArrayQuery, addArrayQuery, parseArrayQuery } from '../../../utils/url';
import { Link } from 'react-router-dom';

interface Props {
    tags:ResData.Tag[];
    getFullList:() => void;
    searchTags:(tags:number[]) => void;
}

interface State {
}

export class Tags extends React.Component<Props, State> {
    public selectedTags:ResData.Tag[] = [];
    public selectedTagIds:number[] = [];
    public filterTags:ResData.Tag[] = [];
    public showFullList = false;

    public clickTag (tag:ResData.Tag) {
        const idx = this.selectedTagIds.indexOf(tag.id);
        if (idx < 0) {
            this.selectedTags.push(tag);
            this.selectedTagIds.push(tag.id);
        } else {
            this.selectedTagIds.splice(idx, 1);
            this.selectedTags.splice(indexEq(this.selectedTags, tag.id), 1);
        }
    }

    public render () {
        let renderTagList:() => JSX.Element;
        if (this.showFullList) {
            renderTagList = this.renderFullTags;
        } else {
            const url = new URL(window.location.href);
            if (this.filterTags.length !== 0) {
                renderTagList = this.renderFilterTags;
            } else {
                renderTagList = this.renderInitTags;
            }
        }
        
        return <Card className="book-tags">
            {renderTagList()}
        </Card>;
    }

    public renderFilterTags = () => {
        return <div className="short_list">
            {this.renderChannels()}
            <div className="buttons">
                <span>筛选标签:</span>
                {this.filterTags.map((tag) => {
                    const idx = this.selectedTagIds.indexOf(tag.id);
                    return <Tag
                        tag={tag}
                        isSelected={idx >= 0}
                        key={tag.id}
                        onClick={() => {
                            this.clickTag(tag);
                            this.props.searchTags(this.selectedTagIds);
                        }}
                />;})}
                <a className="tag" onClick={() => {
                    this.showFullList = true;
                    this.props.getFullList();
                }}>更多</a>
            </div>
        </div>
    }

    public renderInitTags = () => {
        const { tags } = this.props;
        return <div className="short_list">
            {this.renderChannels()}
            <div className="tags">
                {tags.map((tag) => 
                    <Tag tag={tag}
                        onClick={() => {
                            this.clickTag(tag);
                            this.props.searchTags(this.selectedTagIds);
                        }}
                        key={tag.id} />)}
                <a className="tag" onClick={() => {
                    this.showFullList = true;
                    this.props.getFullList();
                }}>更多</a>
            </div>
        </div>;
    }

    public renderChannels = () => {
        const channels = parseArrayQuery(window.location.href, 'channels');
        return <div className="field has-addons" style={{ width: '100%' }}>
            <p className="control" style={{ flex: 1 }}>
                <Channel id={1}
                    text="原创"
                    isSelected={ channels ? (channels.indexOf(1) < 0 ? false : true) : false } />
            </p>
            <p className="control" style={{ flex: 1 }}>
            <Channel id={2}
                    text="同人"
                    isSelected={ channels ? (channels.indexOf(2) < 0 ? false : true) : false } />
            </p>
        </div>;
    }

    public renderFullTags = () => {
        const { tags } = this.props;
        const tagTypes:{[type:string]:ResData.Tag[]} = {};

        for (let i = 0; i < tags.length; i ++) {
            const tag = tags[i];
            const type = tag.attributes.tag_type;
            if (type === '大类') { continue; }
            if (tagTypes[type]) {
                tagTypes[type].push(tag);
            } else {
                tagTypes[type] = [tag];
            }
        }

        return <div className="full_list">
            {Object.keys(tagTypes).map((type, idx) =>
                <div className="li" key={idx}>
                    <div className="tags">
                        <span>{type}</span>
                        {tagTypes[type].map((tag) => {
                            const idx = this.selectedTagIds.indexOf(tag.id)
                            return <Tag
                                onClick={() => {
                                    this.clickTag(tag);
                                }}
                                tag={tag}
                                isSelected={idx >= 0}
                                key={tag.id} />; 
                        })}
                    </div>
                </div>     
            )}
            <div className="li">
                <a className="button is-fullwidth" onClick={() => {}}>点击加载同人标签</a>
            </div>
            <div className="li">
                <a className="button is-fullwidth" onClick={() => {
                    this.showFullList = false;
                    this.filterTags = this.selectedTags.slice();
                    this.props.searchTags(this.selectedTagIds);
                }}>筛选</a>
            </div>
        </div>;
    }
}

class Tag extends React.Component<{
    tag:ResData.Tag;
    className?:string;
    onClick?:(id:number) => void;
    isSelected?:boolean;
}, {
    isSelected:boolean;
}> {
    public state = {
        isSelected: this.props.isSelected || false,
    };

    public render () {
        return <a
            className={(this.props.className || 'tag') + (this.state.isSelected && ' is-primary' || '')}
            onClick={() => {
                this.setState((prevState) => ({ isSelected: !prevState.isSelected }));
                this.props.onClick && this.props.onClick(this.props.tag.id);
            }}
        >{this.props.tag.attributes.tag_name}</a>;
    }
}

class Channel extends React.Component<{
    id:number;
    text:string;
    isSelected?:boolean;
}, {}> {
    public render () {
        return <Link
            className={'button is-fullwidth' + (this.props.isSelected && ' is-primary' || '')}
            to={this.props.isSelected ?
                    removeArrayQuery(window.location.href, 'channels', this.props.id) :
                    addArrayQuery(window.location.href, 'channels', this.props.id)}
        >{this.props.text}</Link>
    }
}
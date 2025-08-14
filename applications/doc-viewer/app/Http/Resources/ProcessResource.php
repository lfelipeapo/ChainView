<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProcessResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'area_id' => $this->area_id,
            'parent_id' => $this->parent_id,
            'type' => $this->type,
            'criticality' => $this->criticality,
            'status' => $this->status,
            'tools' => $this->tools,
            'responsible' => $this->responsible,
            'documentation' => $this->documentation,
            'is_root' => $this->isRoot(),
            'is_subprocess' => $this->isSubprocess(),
            'depth' => $this->getDepth(),
            'area' => $this->when($request->routeIs('*.show'), new AreaResource($this->whenLoaded('area'))),
            'parent' => $this->when($request->routeIs('*.show'), new ProcessResource($this->whenLoaded('parent'))),
            'children' => $this->when($request->routeIs('*.show'), ProcessResource::collection($this->whenLoaded('children'))),
            'children_count' => $this->when($request->routeIs('*.index'), $this->children()->count()),
            'created_at' => $this->created_at ? $this->created_at->toISOString() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toISOString() : null,
        ];
    }
}
